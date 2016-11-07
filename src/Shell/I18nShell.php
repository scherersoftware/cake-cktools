<?php
namespace CkTools\Shell;

use Cake\Cache\Cache;
use Cake\Console\Shell;
use Cake\Core\App;
use Cake\Filesystem\Folder;
use Gettext\Merge;
use Gettext\Translations;

/**
 * I18n Helper Shell
 */
class I18nShell extends Shell
{

    /**
     * Manage the available sub-commands along with their arguments and help
     *
     * @see http://book.cakephp.org/3.0/en/console-and-shells.html#configuring-options-and-generating-help
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        
        $updateFromCatalogParser = parent::getOptionParser();
        $updateFromCatalogParser->addOption('domain', [
            'default' => 'default',
            'help' => 'The domain to use'
        ]);
        $updateFromCatalogParser->addOption('overwrite', [
            'default' => false,
            'help' => 'If set, there will be no interactive question whether to continue.',
            'boolean' => true
        ]);
        $updateFromCatalogParser->addOption('strip-references', [
            'boolean' => true,
            'help' => 'Whether to remove file usage references from resulting po and mo files.'
        ]);
        
        return $parser->description([
            'CkTools Shell',
            '',
            'Utilities'
        ])->addSubcommand('updateFromCatalog', [
            'help' => 'Updates the PO file from the given catalog',
            'parser' => $updateFromCatalogParser
        ]);
        return $parser;
    }

    /**
     * main() method.
     *
     * @return bool|int Success or error code.
     */
    public function main()
    {
        $this->out($this->OptionParser->help());
    }

    /**
     * Updates the catalog file from the given po file
     *
     * @return void
     */
    public function updateFromCatalog()
    {
        $domain = $this->params['domain'];
        #$localePaths = App::path('Locale');

        $localePath = ROOT . '/src/Locale/';

        $catalogFile = $localePath . $domain . '.pot';
        if (!file_exists($catalogFile)) {
            return $this->abort(sprintf('Catalog File %s not found.', $catalogFile));
        }

        $poFiles = [];
        $folder = new Folder($localePath);
        $tree = $folder->tree();
        
        foreach ($tree[1] as $file) {
            $basename = basename($file);
            if ($domain . '.po' === $basename) {
                $poFiles[] = $file;
            }
        }
        if (empty($poFiles)) {
            return $this->abort('I could not find any matching po files in the given locale path (%s) for the domain (%s)', $localePath, $domain);
        }

        if (!$this->params['overwrite']) {
            $this->out('I will update the following .po files and their corresponding .mo file with the keys from catalog: ' . $catalogFile);
            foreach ($poFiles as $poFile) {
                $this->out('    - ' . $poFile);
            }
            $response = $this->in('Would you like to continue?', ['y', 'n'], 'n');
            if ($response !== 'y') {
                return $this->abort('Aborted');
            }
        }
        
        $catalogEntries = Translations::fromPoFile($catalogFile);
        
        foreach ($poFiles as $poFile) {
            $moFile = str_replace('.po', '.mo', $poFile);
            $translationEntries = Translations::fromPoFile($poFile);
            $translationEntries->mergeWith($catalogEntries, Merge::REFERENCES_THEIRS);

            if ($this->params['strip-references']) {
                foreach ($translationEntries as $translation) {
                    $translation->deleteReferences();
                }
            }

            $translationEntries->toPoFile($poFile);
            $translationEntries->toMoFile($moFile);
            $this->out('Updated ' . $poFile);
            $this->out('Updated ' . $moFile);
        }
    }

    /**
     * Welcome
     *
     * @return void
     */
    protected function _welcome()
    {
    }
}

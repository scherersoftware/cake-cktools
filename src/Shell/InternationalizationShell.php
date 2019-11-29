<?php
declare(strict_types = 1);
namespace CkTools\Shell;

use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;
use Cake\Filesystem\Folder;
use Gettext\Merge;
use Gettext\Translations;

/**
 * Internationalization Helper Shell
 */
class InternationalizationShell extends Shell
{

    /**
     * Manage the available sub-commands along with their arguments and help
     *
     * @see http://book.cakephp.org/3.0/en/console-and-shells.html#configuring-options-and-generating-help
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser(): ConsoleOptionParser
    {
        $parser = parent::getOptionParser();

        $updateFromCatalogParser = parent::getOptionParser();
        $updateFromCatalogParser->addOption('domain', [
            'default' => 'default',
            'help' => 'The domain to use',
        ]);
        $updateFromCatalogParser->addOption('overwrite', [
            'default' => false,
            'help' => 'If set, there will be no interactive question whether to continue.',
            'boolean' => true,
        ]);
        $updateFromCatalogParser->addOption('strip-references', [
            'boolean' => true,
            'help' => 'Whether to remove file usage references from resulting po and mo files.',
        ]);

        return $parser
            ->setDescription([
                'CkTools Shell',
                '',
                'Utilities',
            ])
            ->addSubcommand('updateFromCatalog', [
                'help' => 'Updates the PO file from the given catalog',
                'parser' => $updateFromCatalogParser,
            ]);
    }

    /**
     * main() method.
     *
     * @return void
     */
    public function main(): void
    {
        $this->out($this->OptionParser->help());
    }

    /**
     * Updates the catalog file from the given po file
     *
     * @return void
     */
    public function updateFromCatalog(): void
    {
        $domain = $this->params['domain'];

        $localePath = ROOT . '/src/Locale/';

        $catalogFile = $localePath . $domain . '.pot';
        if (!file_exists($catalogFile)) {
            $this->abort(sprintf('Catalog File %s not found.', $catalogFile));
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
            $this->abort(sprintf(
                'I could not find any matching po files in the given locale path (%s) for the domain (%s)',
                $localePath,
                $domain
            ));
        }

        if (!$this->params['overwrite']) {
            $this->out(
                'I will update the following .po files and their .mo file with the keys from catalog: ' . $catalogFile
            );
            foreach ($poFiles as $poFile) {
                $this->out('    - ' . $poFile);
            }
            $response = $this->in('Would you like to continue?', ['y', 'n'], 'n');
            if ($response !== 'y') {
                $this->abort('Aborted');
            }
        }

        foreach ($poFiles as $poFile) {
            $catalogEntries = Translations::fromPoFile($catalogFile);
            $moFile = str_replace('.po', '.mo', $poFile);
            $translationEntries = Translations::fromPoFile($poFile);

            $newTranslationEntries = $catalogEntries->mergeWith($translationEntries, Merge::REFERENCES_THEIRS);

            $newTranslationEntries->deleteHeaders();
            foreach ($translationEntries->getHeaders() as $key => $value) {
                $newTranslationEntries->setHeader($key, $value);
            }

            if ($this->params['strip-references']) {
                foreach ($newTranslationEntries as $translation) {
                    $translation->deleteReferences();
                }
            }

            $newTranslationEntries->toPoFile($poFile);
            $newTranslationEntries->toMoFile($moFile);
            $this->out('Updated ' . $poFile);
            $this->out('Updated ' . $moFile);
        }
    }

    /**
     * Welcome
     *
     * @return void
     */
    protected function _welcome(): void
    {
    }
}

<?php
declare(strict_types = 1);
namespace CkTools\Shell;

use Cake\Cache\Cache;
use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;

/**
 * CkTools shell command.
 */
class CkToolsShell extends Shell
{

    /**
     * Manage the available sub-commands along with their arguments and help
     *
     * @see http://book.cakephp.org/3.0/en/console-and-shells.html#configuring-options-and-generating-help
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser(): ConsoleOptionParser
    {
        $parser = parent::getOptionParser();

        return $parser
            ->setDescription([
                'CkTools Shell',
                '',
                'Utilities',
            ])
            ->addSubcommand('clearCache', [
                'help' => 'Clears data of all configured Cache engines.',
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
     * Clears data of all configured Cache engines.
     *
     * @return void
     */
    public function clearCache(): void
    {
        Cache::clearAll();
        $this->out('All caches cleared');
    }
}

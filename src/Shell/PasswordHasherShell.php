<?php
declare(strict_types = 1);
namespace CkTools\Shell;

use Cake\Auth\DefaultPasswordHasher;
use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;

/**
 * PasswordHasher shell command.
 */
class PasswordHasherShell extends Shell
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
        return parent::getOptionParser();
    }

    /**
     * main() method.
     *
     * @return bool|int|null Success or error code.
     */
    public function main(): void
    {
        if (empty($this->args[0])) {
            $this->err('Please provide a password string.', 2);

            return;
        }

        $hasher = new DefaultPasswordHasher;
        $passwordToHash = $this->args[0];
        $hashedPassword = $hasher->hash($passwordToHash);

        $this->out('Your password: ' . $hashedPassword, 2);
    }

    /**
     * Displays the welcome message
     *
     * @return void
     */
    protected function _welcome(): void
    {
        $this->out('This shell generates a hashed password by a given string.');
        $this->hr();
        $this->out();
    }
}

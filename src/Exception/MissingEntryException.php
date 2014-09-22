<?php
namespace CkTools\Exception;

use Cake\Core\Exception\Exception;

/**
 * Exception raised when an entry could not be found
 *
 */
class MissingEntryException extends Exception {

	protected $_messageTemplate = 'Given %s does not exist.';
}

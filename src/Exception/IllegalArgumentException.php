<?php
namespace CkTools\Exception;

use Cake\Core\Exception\Exception;

/**
 * Exception raised when an entry could not be found
 *
 */
class IllegalArgumentException extends Exception {

	protected $_messageTemplate = '%s';
}

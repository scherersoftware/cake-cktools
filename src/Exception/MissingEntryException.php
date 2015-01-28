<?php
namespace CkTools\Exception;

use Cake\Core\Exception\Exception;

/**
 * Exception raised when an entry could not be found
 *
 */
class MissingEntryException extends Exception
{

    protected $_messageTemplate = 'Given %s does not exist.';

    /**
     * sets default code to 404
     *
     * @param string|array $message Either the string of the error message, or an array of attributes
     *   that are made available in the view, and sprintf()'d into Exception::$_messageTemplate
     * @param int $code The code of the error, is also the HTTP status code for the error.
     * @param \Exception $previous the previous exception.
     */
    public function __construct($message, $code = 404, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

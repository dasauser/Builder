<?php
namespace Dasauser\Exceptions;

use Throwable;

/**
 * Custom exception
 * Class FileNotFoundException
 * @package Dasauser\Exceptions
 */
class FileNotFoundException extends \Exception
{
    /**
     * FileNotFoundException constructor
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "", $code = 404, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
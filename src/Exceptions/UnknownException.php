<?php
namespace Dasauser\Exceptions;

use Throwable;

/**
 * Unknown whatever custom exception
 * Class UnknownException
 * @package Dasauser\Exceptions
 */
class UnknownException extends \Exception
{
    /**
     * UnknownException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "", $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
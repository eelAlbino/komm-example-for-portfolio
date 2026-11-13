<?php
namespace App\Exceptions;

use Exception as Base;
use Throwable;

class Exception extends Base
{

    protected ?string $errorCode = null;

    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null, ?string $errorCode = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errorCode = $errorCode;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }
}

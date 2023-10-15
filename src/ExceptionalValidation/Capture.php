<?php

declare(strict_types=1);

namespace PhDBus\ExceptionalValidation;

use Attribute;

/** @readonly */
#[Attribute(Attribute::TARGET_PROPERTY|Attribute::IS_REPEATABLE)]
final class Capture
{
    public function __construct(
        private string $exception,
        private string $message,
    ) {
    }

    public function getExceptionClass(): string
    {
        return $this->exception;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}

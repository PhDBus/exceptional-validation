<?php

declare(strict_types=1);

namespace PhDBus\ExceptionalValidation\CaptureTree;

use Exception;
use PhDBus\ExceptionalValidation\Capture;
use PhDBus\ExceptionalValidation\CaptureTree;

final class CaptureItem implements CaptureTree
{
    public function __construct(
        private CaptureList $parent,
        private Capture $capture,
    ) {
    }

    public function catchException(Exception $exception): ?self
    {
        $exceptionClass = $this->capture->getExceptionClass();

        if (!$exception instanceof $exceptionClass) {
            return null;
        }

        return $this;
    }

    public function getPropertyPath(): array
    {
        return $this->parent->getPropertyPath();
    }

    public function getMessage(): string
    {
        return $this->capture->getMessage();
    }

    public function getRoot(): object
    {
        return $this->parent->getRoot();
    }
}

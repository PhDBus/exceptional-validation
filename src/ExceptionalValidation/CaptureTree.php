<?php

declare(strict_types=1);

namespace PhDBus\ExceptionalValidation;

use Exception;
use PhDBus\ExceptionalValidation\CaptureTree\CaptureItem;

interface CaptureTree
{
    public function catchException(Exception $exception): ?CaptureItem;

    public function getPropertyPath(): array;

    public function getRoot(): object;
}

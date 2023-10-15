<?php

declare(strict_types=1);

namespace PhDBus\ExceptionalValidation\CaptureTree\Assembler;

use PhDBus\ExceptionalValidation\CaptureTree\CapturableObject;
use PhDBus\ExceptionalValidation\CaptureTree\CapturableProperty;

interface CapturableObjectAssembler
{
    public function assembleTree(?CapturableProperty $parentProperty = null): CapturableObject;
}

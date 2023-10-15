<?php

declare(strict_types=1);

namespace PhDBus\ExceptionalValidation\CaptureTree\Assembler;

use PhDBus\ExceptionalValidation\CaptureTree\CapturableObject;

interface CapturableObjectPropertiesAssembler
{
    public function assembleProperties(CapturableObject $captureObject): iterable;
}

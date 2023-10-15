<?php

declare(strict_types=1);

namespace PhDBus\ExceptionalValidation\CaptureTree\Assembler;

use PhDBus\ExceptionalValidation\CaptureTree\CapturableProperty;
use PhDBus\ExceptionalValidation\CaptureTree\CaptureList;

interface CaptureListAssembler
{
    public function assembleList(CapturableProperty $property): CaptureList;
}

<?php

declare(strict_types=1);

namespace PhDBus\ExceptionalValidation\Internal\Assembler\Factory;

use PhDBus\ExceptionalValidation\CaptureTree\Assembler\CaptureListItemsAssembler;
use PhDBus\ExceptionalValidation\Internal\Assembler\CaptureListItemsViaReflectionAssembler;

/** @internal */
final class CaptureListItemsAssemblerFactory
{
    public function create(array $captureAttributes): CaptureListItemsAssembler
    {
        return new CaptureListItemsViaReflectionAssembler($captureAttributes);
    }
}

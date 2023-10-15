<?php

declare(strict_types=1);

namespace PhDBus\ExceptionalValidation\Internal\Assembler\Factory;

use PhDBus\ExceptionalValidation\Capture;
use PhDBus\ExceptionalValidation\CaptureTree\Assembler\CaptureListAssembler;
use PhDBus\ExceptionalValidation\Internal\Assembler\CaptureListViaReflectionAssembler;
use ReflectionAttribute;
use ReflectionProperty;

/** @internal */
final class CaptureListAssemblerFactory
{
    public function __construct(
        private CaptureListItemsAssemblerFactory $captureListItemsAssemblerFactory,
    ) {
    }

    public function createForProperty(ReflectionProperty $property): ?CaptureListAssembler
    {
        $captureAttributes = $property->getAttributes(Capture::class, ReflectionAttribute::IS_INSTANCEOF);

        if ([] === $captureAttributes) {
            return null;
        }

        return new CaptureListViaReflectionAssembler($this->captureListItemsAssemblerFactory, $captureAttributes);
    }
}

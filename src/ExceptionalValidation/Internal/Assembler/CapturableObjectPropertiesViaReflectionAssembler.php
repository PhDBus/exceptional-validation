<?php

declare(strict_types=1);

namespace PhDBus\ExceptionalValidation\Internal\Assembler;

use PhDBus\ExceptionalValidation\CaptureTree\Assembler\CapturableObjectPropertiesAssembler;
use PhDBus\ExceptionalValidation\CaptureTree\CapturableObject;
use PhDBus\ExceptionalValidation\CaptureTree\CapturableProperty;
use PhDBus\ExceptionalValidation\Internal\Assembler\Factory\CaptureListAssemblerFactory;
use PhDBus\ExceptionalValidation\Internal\Assembler\Factory\NestedCapturableObjectAssemblerFactory;
use ReflectionProperty;

/** @internal */
final class CapturableObjectPropertiesViaReflectionAssembler implements CapturableObjectPropertiesAssembler
{
    public function __construct(
        private CaptureListAssemblerFactory $captureListAssemblerFactory,
        private NestedCapturableObjectAssemblerFactory $nestedCaptureTreeAssemblerFactory,
        private array $reflectionProperties,
        private object $message,
    ) {
    }

    public function assembleProperties(CapturableObject $captureObject): iterable
    {
        /** @var ReflectionProperty $property */
        foreach ($this->reflectionProperties as $property) {
            $name = $property->getName();

            $captureListAssembler = $this->captureListAssemblerFactory->createForProperty($property);
            $nestedObjectTreeAssembler = $this->nestedCaptureTreeAssemblerFactory->createForProperty($this->message, $property);

            if (null === $captureListAssembler && null === $nestedObjectTreeAssembler) {
                continue;
            }

            yield CapturableProperty::asChildOf($captureObject, $name, $captureListAssembler, $nestedObjectTreeAssembler);
        }
    }
}

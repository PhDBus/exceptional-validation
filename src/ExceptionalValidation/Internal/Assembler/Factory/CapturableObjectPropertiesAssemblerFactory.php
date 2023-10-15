<?php

declare(strict_types=1);

namespace PhDBus\ExceptionalValidation\Internal\Assembler\Factory;

use PhDBus\ExceptionalValidation\CaptureTree\Assembler\CapturableObjectPropertiesAssembler;
use PhDBus\ExceptionalValidation\Internal\Assembler\CapturableObjectPropertiesViaReflectionAssembler;
use ReflectionClass;

/** @internal */
final class CapturableObjectPropertiesAssemblerFactory
{
    public function __construct(
        private CaptureListAssemblerFactory $captureListAssemblerFactory,
        private NestedCapturableObjectAssemblerFactory $nestedCaptureTreeAssemblerFactory,
    ) {
    }

    public function createForObject(ReflectionClass $reflectionClass, object $message): CapturableObjectPropertiesAssembler
    {
        return new CapturableObjectPropertiesViaReflectionAssembler(
            $this->captureListAssemblerFactory,
            $this->nestedCaptureTreeAssemblerFactory,
            $reflectionClass->getProperties(),
            $message,
        );
    }
}

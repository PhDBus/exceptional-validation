<?php

declare(strict_types=1);

namespace PhDBus\ExceptionalValidation\Internal\Assembler;

use PhDBus\ExceptionalValidation\CaptureTree\Assembler\CapturableObjectAssembler;
use PhDBus\ExceptionalValidation\CaptureTree\CapturableObject;
use PhDBus\ExceptionalValidation\CaptureTree\CapturableProperty;
use PhDBus\ExceptionalValidation\Internal\Assembler\Factory\CapturableObjectPropertiesAssemblerFactory;
use ReflectionClass;

/** @internal */
final class CapturableObjectViaReflectionAssembler implements CapturableObjectAssembler
{
    public function __construct(
        private CapturableObjectPropertiesAssemblerFactory $propertiesAssemblerFactory,
        private ReflectionClass $reflectionClass,
        private object $message,
    ) {
    }

    public function assembleTree(?CapturableProperty $parentProperty = null): CapturableObject
    {
        $propertiesAssembler = $this->propertiesAssemblerFactory->createForObject($this->reflectionClass, $this->message);

        return CapturableObject::compose($this->message, $parentProperty, $propertiesAssembler);
    }
}

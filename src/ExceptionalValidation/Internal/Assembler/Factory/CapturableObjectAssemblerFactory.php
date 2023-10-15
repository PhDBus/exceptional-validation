<?php

declare(strict_types=1);

namespace PhDBus\ExceptionalValidation\Internal\Assembler\Factory;

use PhDBus\ExceptionalValidation;
use PhDBus\ExceptionalValidation\CaptureTree\Assembler\CapturableObjectAssembler;
use PhDBus\ExceptionalValidation\Internal\Assembler\CapturableObjectViaReflectionAssembler;
use ReflectionAttribute;
use ReflectionClass;

/** @internal */
final class CapturableObjectAssemblerFactory
{
    public function __construct(
        private CapturableObjectPropertiesAssemblerFactory $propertiesAssemblerFactory,
    ) {
    }

    public function createForObject(object $object): ?CapturableObjectAssembler
    {
        $reflectionClass = new ReflectionClass($object);

        if ([] === $reflectionClass->getAttributes(ExceptionalValidation::class, ReflectionAttribute::IS_INSTANCEOF)) {
            return null;
        }

        return new CapturableObjectViaReflectionAssembler($this->propertiesAssemblerFactory, $reflectionClass, $object);
    }
}

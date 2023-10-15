<?php

declare(strict_types=1);

namespace PhDBus\ExceptionalValidation\Internal\Assembler\Factory;

use PhDBus\ExceptionalValidation\CaptureTree\Assembler\CapturableObjectAssembler;
use ReflectionAttribute;
use ReflectionProperty;
use Symfony\Component\Validator\Constraints\Valid;

/** @internal */
final class NestedCapturableObjectAssemblerFactory
{
    private CapturableObjectAssemblerFactory $objectAssemblerFactory;

    public function setObjectAssemblerFactory(CapturableObjectAssemblerFactory $objectAssemblerFactory): void
    {
        $this->objectAssemblerFactory = $objectAssemblerFactory;
    }

    public function createForProperty(object $rootObject, ReflectionProperty $property): ?CapturableObjectAssembler
    {
        if (!$property->isInitialized($rootObject)) {
            return null;
        }

        $propertyValue = $property->getValue($rootObject);

        if (!is_object($propertyValue)) {
            return null;
        }

        $validAttributes = $property->getAttributes(Valid::class, ReflectionAttribute::IS_INSTANCEOF);

        if ([] === $validAttributes) {
            return null;
        }

        return $this->objectAssemblerFactory->createForObject($propertyValue);
    }
}

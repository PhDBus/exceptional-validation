<?php

declare(strict_types=1);

namespace PhDBus\ExceptionalValidation\CaptureTree;

use Exception;
use PhDBus\ExceptionalValidation\CaptureTree;
use PhDBus\ExceptionalValidation\CaptureTree\Assembler\CapturableObjectPropertiesAssembler;

final class CapturableObject implements CaptureTree
{
    private function __construct(
        private object $object,
        private ?CapturableProperty $parentProperty,
        /** @var iterable<CapturableProperty> */
        private iterable $captureProperties,
    ) {
    }

    public static function compose(object $object, ?CapturableProperty $parentProperty, CapturableObjectPropertiesAssembler $propertiesAssembler): self
    {
        $self = new self($object, $parentProperty, []);

        $self->captureProperties = $propertiesAssembler->assembleProperties($self);

        return $self;
    }

    public function catchException(Exception $exception): ?CaptureItem
    {
        foreach ($this->captureProperties as $property) {
            if ($hit = $property->catchException($exception)) {
                return $hit;
            }
        }

        return null;
    }

    public function getPropertyPath(): array
    {
        return $this->parentProperty?->getPropertyPath() ?? [];
    }

    public function getRoot(): object
    {
        return $this->parentProperty?->getRoot() ?? $this->object;
    }
}

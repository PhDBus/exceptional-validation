<?php

declare(strict_types=1);

namespace PhDBus\ExceptionalValidation\CaptureTree;

use Exception;
use LogicException;
use PhDBus\ExceptionalValidation\CaptureTree;
use PhDBus\ExceptionalValidation\CaptureTree\Assembler\CapturableObjectAssembler;
use PhDBus\ExceptionalValidation\CaptureTree\Assembler\CaptureListAssembler;

final class CapturableProperty implements CaptureTree
{
    private function __construct(
        private CapturableObject $parent,
        private string $name,
        private ?CaptureList $captureList,
        private ?CapturableObject $nestedObject,
    ) {
    }

    public static function asChildOf(
        CapturableObject $parent,
        string $name,
        ?CaptureListAssembler $captureListAssembler,
        ?CapturableObjectAssembler $nestedObjectAssembler
    ): self {
        $self = new self($parent, $name, null, null);

        $self->captureList = $captureListAssembler?->assembleList($self);
        $self->nestedObject = $nestedObjectAssembler?->assembleTree($self);

        if ($self->captureList === null && $self->nestedObject === null) {
            throw new LogicException('CaptureProperty must have at least one child');
        }

        return $self;
    }

    public function catchException(Exception $exception): ?CaptureItem
    {
        return $this->captureList?->catchException($exception)
            ?? $this->nestedObject?->catchException($exception);
    }

    public function getPropertyPath(): array
    {
        return [...$this->parent->getPropertyPath(), $this->name];
    }

    public function getRoot(): object
    {
        return $this->parent->getRoot();
    }
}

<?php

declare(strict_types=1);

namespace PhDBus\ExceptionalValidation\CaptureTree;

use Exception;
use PhDBus\ExceptionalValidation\CaptureTree;
use PhDBus\ExceptionalValidation\CaptureTree\Assembler\CaptureListItemsAssembler;

final class CaptureList implements CaptureTree
{
    public function __construct(
        private CapturableProperty $property,
        /** @var iterable<CaptureItem> $captures */
        private iterable $captures,
    ) {
    }

    public static function compose(CapturableProperty $property, CaptureListItemsAssembler $itemsAssembler): self
    {
        $self = new self($property, []);

        $self->captures = $itemsAssembler->assembleListItems($self);

        return $self;
    }

    public function catchException(Exception $exception): ?CaptureItem
    {
        foreach ($this->captures as $capture) {
            if ($hit = $capture->catchException($exception)) {
                return $hit;
            }
        }

        return null;
    }

    public function getPropertyPath(): array
    {
        return $this->property->getPropertyPath();
    }

    public function getRoot(): object
    {
        return $this->property->getRoot();
    }
}

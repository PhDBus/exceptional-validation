<?php

declare(strict_types=1);

namespace PhDBus\ExceptionalValidation\Internal\Assembler;

use PhDBus\ExceptionalValidation\CaptureTree\Assembler\CaptureListAssembler;
use PhDBus\ExceptionalValidation\CaptureTree\CapturableProperty;
use PhDBus\ExceptionalValidation\CaptureTree\CaptureList;
use PhDBus\ExceptionalValidation\Internal\Assembler\Factory\CaptureListItemsAssemblerFactory;

/** @internal */
final class CaptureListViaReflectionAssembler implements CaptureListAssembler
{
    public function __construct(
        private CaptureListItemsAssemblerFactory $captureListItemsAssemblerFactory,
        private array $captureAttributes,
    ) {
    }

    public function assembleList(CapturableProperty $property): CaptureList
    {
        $itemsAssembler = $this->captureListItemsAssemblerFactory->create($this->captureAttributes);

        return CaptureList::compose($property, $itemsAssembler);
    }
}

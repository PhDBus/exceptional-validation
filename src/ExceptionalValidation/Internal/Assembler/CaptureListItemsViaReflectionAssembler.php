<?php

declare(strict_types=1);

namespace PhDBus\ExceptionalValidation\Internal\Assembler;

use PhDBus\ExceptionalValidation\CaptureTree\Assembler\CaptureListItemsAssembler;
use PhDBus\ExceptionalValidation\CaptureTree\CaptureItem;
use PhDBus\ExceptionalValidation\CaptureTree\CaptureList;

/** @internal */
final class CaptureListItemsViaReflectionAssembler implements CaptureListItemsAssembler
{
    public function __construct(
        private array $captureAttributes,
    ) {
    }

    public function assembleListItems(CaptureList $captureList): iterable
    {
        foreach ($this->captureAttributes as $captureAttribute) {
            $capture = $captureAttribute->newInstance();

            yield new CaptureItem($captureList, $capture);
        }
    }
}

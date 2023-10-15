<?php

declare(strict_types=1);

namespace PhDBus\ExceptionalValidation\CaptureTree\Assembler;

use PhDBus\ExceptionalValidation\CaptureTree\CaptureList;

interface CaptureListItemsAssembler
{
    public function assembleListItems(CaptureList $captureList): iterable;
}

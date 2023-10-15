<?php

declare(strict_types=1);

namespace PhDBus\Tests\Stub;

use LogicException;
use PhDBus\ExceptionalValidation;

final class NotHandleableMessageStub
{
    #[ExceptionalValidation\Capture(LogicException::class, 'oops')]
    private string $messageText;
}

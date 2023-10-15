<?php

declare(strict_types=1);

namespace PhDBus\Tests\Stub;

use LogicException;
use PhDBus\ExceptionalValidation;
use PhDBus\Tests\Stub\Exception\OrdinaryPropertyCapturedException;
use PhDBus\Tests\Stub\Exception\StaticPropertyCapturedException;

#[ExceptionalValidation]
final class HandleableMessageStub
{
    #[ExceptionalValidation\Capture(LogicException::class, 'oops')]
    private string $messageText;

    #[ExceptionalValidation\Capture(OrdinaryPropertyCapturedException::class, 'oops')]
    private object $someObject;

    #[ExceptionalValidation\Capture(StaticPropertyCapturedException::class, 'oops')]
    private static string $staticProperty;
}

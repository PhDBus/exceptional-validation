<?php

declare(strict_types=1);

namespace PhDBus\Tests\Messenger;

use LogicException;
use PhDBus\ExceptionalValidation\Exception\ExceptionalValidationFailedException;
use PhDBus\ExceptionalValidation\Internal\Assembler\Factory\CaptureListAssemblerFactory;
use PhDBus\ExceptionalValidation\Internal\Assembler\Factory\CapturableObjectPropertiesAssemblerFactory;
use PhDBus\ExceptionalValidation\Internal\Assembler\Factory\CapturableObjectAssemblerFactory;
use PhDBus\ExceptionalValidation\Internal\Assembler\Factory\CaptureListItemsAssemblerFactory;
use PhDBus\ExceptionalValidation\Internal\Assembler\Factory\NestedCapturableObjectAssemblerFactory;
use PhDBus\ExceptionalValidation\Messenger\ExceptionalValidationMiddleware;
use PhDBus\ExceptionalValidation\Validator\Factory\ValidatorFactory;
use PhDBus\Tests\Stub\Exception\OrdinaryPropertyCapturedException;
use PhDBus\Tests\Stub\Exception\StaticPropertyCapturedException;
use PhDBus\Tests\Stub\HandleableMessageStub;
use PhDBus\Tests\Stub\HandleableMessageWithStaticProperty;
use PhDBus\Tests\Stub\NotHandleableMessageStub;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackMiddleware;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @covers \PhDBus\ExceptionalValidation\Messenger\ExceptionalValidationMiddleware
 */
final class ExceptionalValidationMiddlewareTest extends TestCase
{
    private ExceptionalValidationMiddleware $middleware;
    private $translator;
    private $nextMiddleware;
    private $stack;

    protected function setUp(): void
    {
        parent::setUp();

        $this->translator = $this->createMock(TranslatorInterface::class);

        $validatorFactory = new ValidatorFactory($this->translator, 'domain');
        $this->middleware = new ExceptionalValidationMiddleware($validatorFactory);
        $this->nextMiddleware = $this->createMock(MiddlewareInterface::class);
        $this->stack = new StackMiddleware([$this->middleware, $this->nextMiddleware]);
    }

    public function testHandlesMessageThroughStack(): void
    {
        $envelope = Envelope::wrap(new HandleableMessageStub());
        $resultEnvelope = Envelope::wrap(new stdClass());

        $this->nextMiddleware
            ->method('handle')
            ->willReturnMap([[$envelope, $this->stack, $resultEnvelope]])
        ;

        $result = $this->middleware->handle($envelope, $this->stack);

        self::assertSame($resultEnvelope, $result);
    }

    public function testRethrowsHandlerFailedException(): void
    {
        $envelope = Envelope::wrap(new HandleableMessageStub());

        $this->nextMiddleware
            ->method('handle')
            ->willThrowException(new HandlerFailedException($envelope, [new LogicException()]))
        ;

        $this->expectException(HandlerFailedException::class);

        $this->middleware->handle($envelope, $this->stack);
    }

    public function testDoesNotCaptureExceptionForMessageWithoutExceptionalValidationAttribute(): void
    {
        $envelope = Envelope::wrap(new NotHandleableMessageStub());

        $this->nextMiddleware
            ->method('handle')
            ->willThrowException(new LogicException('Some exception'))
        ;

        $this->expectException(LogicException::class);

        try {
            $this->middleware->handle($envelope, $this->stack);
        } catch (LogicException $e) {
            self::assertSame('Some exception', $e->getMessage());

            throw $e;
        }
    }

    public function testCapturesExceptionMappedToProperty(): void
    {
        $message = new HandleableMessageStub();
        $envelope = Envelope::wrap($message);

        $nextMiddlewareException = new OrdinaryPropertyCapturedException();

        $this->nextMiddleware
            ->method('handle')
            ->willThrowException($nextMiddlewareException)
        ;

        $this->expectException(ExceptionalValidationFailedException::class);

        try {
            $this->middleware->handle($envelope, $this->stack);
        } catch (ExceptionalValidationFailedException $e) {
            self::assertSame('Message of type "PhDBus\Tests\Stub\HandleableMessageStub" has failed exceptional validation.', $e->getMessage());
            self::assertSame($nextMiddlewareException, $e->getPrevious());
            self::assertSame($message, $e->getViolatingMessage());

            $violationList = $e->getViolations();
            self::assertCount(1, $violationList);

            /** @var ConstraintViolationInterface $violation */
            $violation = $violationList[0];
            self::assertSame('someObject', $violation->getPropertyPath());
            self::assertSame('oops', $violation->getMessageTemplate());
            self::assertSame($message, $violation->getRoot());
            self::assertSame([], $violation->getParameters());
            self::assertNull($violation->getInvalidValue());

            throw $e;
        }
    }

    public function testCapturesExceptionsMappedToStaticProperties(): void
    {
        $message = new HandleableMessageStub();
        $envelope = Envelope::wrap($message);

        $nextMiddlewareException = new StaticPropertyCapturedException('Some exception');

        $this->nextMiddleware
            ->method('handle')
            ->willThrowException($nextMiddlewareException)
        ;

        $this->expectException(ExceptionalValidationFailedException::class);

        $this->middleware->handle($envelope, $this->stack);
    }
}

<?php

declare(strict_types=1);

namespace PhDBus\ExceptionalValidation\Messenger;

use Exception;
use PhDBus\ExceptionalValidation\Exception\ExceptionalValidationFailedException;
use PhDBus\ExceptionalValidation\Validator\Factory\ValidatorFactory;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Throwable;

/** @readonly */
final class ExceptionalValidationMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ValidatorFactory $validatorFactory,
    ) {
    }

    /** @throws Throwable */
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $msg = $envelope->getMessage();

        try {
            return $stack->next()->handle($envelope, $stack);
        } catch (Exception $exception) {
            $validator = $this->validatorFactory->getMessageValidator($msg);

            if (null === $validator) {
                throw $exception;
            }

            $violations = $validator->capture($exception);

            throw new ExceptionalValidationFailedException($msg, $violations, $exception);
        }
    }
}

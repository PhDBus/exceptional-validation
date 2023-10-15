<?php

declare(strict_types=1);

namespace PhDBus\ExceptionalValidation\Exception;

use RuntimeException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

final class ExceptionalValidationFailedException extends RuntimeException
{
    private object $violatingMessage;

    private ConstraintViolationListInterface $violations;

    public function __construct(object $violatingMessage, ConstraintViolationListInterface $violations, Throwable $previous)
    {
        $this->violatingMessage = $violatingMessage;
        $this->violations = $violations;

        parent::__construct(
            sprintf('Message of type "%s" has failed exceptional validation.', $this->violatingMessage::class),
            previous: $previous,
        );
    }

    public function getViolatingMessage(): object
    {
        return $this->violatingMessage;
    }

    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }
}

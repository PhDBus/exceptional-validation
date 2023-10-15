<?php

declare(strict_types=1);

namespace PhDBus\ExceptionalValidation\Validator;

use Exception;
use PhDBus\ExceptionalValidation\CaptureTree;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Contracts\Translation\TranslatorInterface;

final class MessageCaptureValidator
{
    public function __construct(
        private TranslatorInterface $translator,
        private string $translationDomain,
        private CaptureTree $captureTree,
    ) {
    }

    /** @throws Exception */
    public function capture(Exception $exception): ConstraintViolationList
    {
        $capturedItem = $this->captureTree->catchException($exception);

        if (null === $capturedItem) {
            throw $exception;
        }

        $violations = new ConstraintViolationList();

        $this->addViolation($violations, $capturedItem);

        return $violations;
    }

    private function addViolation(ConstraintViolationList $violations, CaptureTree\CaptureItem $capturedItem): void
    {
        $root = $capturedItem->getRoot();
        $propertyPath = $capturedItem->getPropertyPath();
        $message = $capturedItem->getMessage();

        $translatedMessage = $this->translator->trans($message, domain: $this->translationDomain);

        $violations->add(
            new ConstraintViolation(
                $translatedMessage,
                $message,
                [],
                $root,
                $this->buildPropertyPath($propertyPath),
                invalidValue: null,
            ),
        );
    }

    private function buildPropertyPath(array $properties): string
    {
        return implode('.', $properties);
    }
}

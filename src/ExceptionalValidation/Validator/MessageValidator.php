<?php

declare(strict_types=1);

namespace PhDBus\ExceptionalValidation\Validator;

use Exception;
use PhDBus\ExceptionalValidation\Capture;
use PhDBus\ExceptionalValidation\Exception\ExceptionalValidationFailedException;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

final class MessageValidator
{
    public function __construct(
        private TranslatorInterface $translator,
        private string $translationDomain,
        private ReflectionClass $reflectionClass,
    ) {
    }

    public function capture(object $message, Exception $exception): void
    {
        // when exception occurs,
        // we need to scan over properties of message
        // find which have Capture attribute
        // if that attribute exception class matches to caught exception,
        // then build a property path and add a violation to the list

        [$attribute, $properties] = $this->getCaptureAttributeRecursive($this->reflectionClass, $exception);

        $violations = new ConstraintViolationList();

        $this->addViolation($message, $violations, $attribute, $properties);

        throw new ExceptionalValidationFailedException($message, $violations, $exception);
    }

    private function getCaptureAttributeRecursive(ReflectionClass $reflectionClass, Throwable $exception): array
    {
        $properties = $reflectionClass->getProperties();

        $nestedProperties = [];

        foreach ($properties as $property) {
            $captures = $property->getAttributes(Capture::class, ReflectionAttribute::IS_INSTANCEOF);

            if ([] === $captures) {
                if ([] === $property->getAttributes(Valid::class, ReflectionAttribute::IS_INSTANCEOF)) {
                    continue;
                }

                $nestedProperties[] = $property;
            }

            foreach ($captures as $capture) {
                /** @var Capture $attribute */
                $attribute = $capture->newInstance();
                $exceptionalClass = $attribute->getExceptionClass();

                if ($exception instanceof $exceptionalClass) {
                    return [$attribute, [$property]];
                }
            }
        }

        foreach ($nestedProperties as $nestedProperty) {
            $propertyType = $nestedProperty->getType();

            if (!$propertyType instanceof ReflectionNamedType) {
                continue;
            }

            $nestedClassName = $propertyType->getName();

            if (!class_exists($nestedClassName)) {
                continue;
            }

            if (null !== $attr = $this->getCaptureAttributeRecursive(new ReflectionClass($nestedClassName), $exception)) {
                return [$attr[0], [$nestedProperty, ...$attr[1]]];
            }
        }

        throw $exception;
    }

    private function addViolation(object $msg, ConstraintViolationList $violations, Capture $attribute, array $properties): void
    {
        $translatedMessage = $this->translator->trans($attribute->getMessage(), domain: $this->translationDomain);

        $violations->add(
            new ConstraintViolation(
                $translatedMessage,
                $attribute->getMessage(),
                [],
                $msg,
                $this->buildPropertyPath($properties),
                invalidValue: null,
            ),
        );
    }

    private function buildPropertyPath(array $properties): string
    {
        return implode(
            '.',
            array_map(static fn (ReflectionProperty $p): string => $p->getName(), $properties),
        );
    }
}

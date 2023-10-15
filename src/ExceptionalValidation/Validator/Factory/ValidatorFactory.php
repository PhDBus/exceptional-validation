<?php

declare(strict_types=1);

namespace PhDBus\ExceptionalValidation\Validator\Factory;

use PhDBus\ExceptionalValidation\Internal\Assembler\Factory\CapturableObjectAssemblerFactory;
use PhDBus\ExceptionalValidation\Internal\Assembler\Factory\CapturableObjectPropertiesAssemblerFactory;
use PhDBus\ExceptionalValidation\Internal\Assembler\Factory\CaptureListAssemblerFactory;
use PhDBus\ExceptionalValidation\Internal\Assembler\Factory\CaptureListItemsAssemblerFactory;
use PhDBus\ExceptionalValidation\Internal\Assembler\Factory\NestedCapturableObjectAssemblerFactory;
use PhDBus\ExceptionalValidation\Validator\MessageCaptureValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ValidatorFactory
{
    private CapturableObjectAssemblerFactory $treeAssemblerFactory;

    public function __construct(
        private TranslatorInterface $translator,
        private string $translationDomain,
    ) {
        $treeAssemblerFactory = new CapturableObjectAssemblerFactory(
            new CapturableObjectPropertiesAssemblerFactory(
                new CaptureListAssemblerFactory(
                    new CaptureListItemsAssemblerFactory(),
                ),
                $nestedCaptureTreeAssemblerFactory = new NestedCapturableObjectAssemblerFactory(),
            ),
        );
        $nestedCaptureTreeAssemblerFactory->setObjectAssemblerFactory($treeAssemblerFactory);

        $this->treeAssemblerFactory = $treeAssemblerFactory;
    }

    public function getMessageValidator(object $object): ?MessageCaptureValidator
    {
        $treeAssembler = $this->treeAssemblerFactory->createForObject($object);

        if (null === $treeAssembler) {
            return null;
        }

        $captureTree = $treeAssembler->assembleTree();

        return new MessageCaptureValidator($this->translator, $this->translationDomain, $captureTree);
    }
}

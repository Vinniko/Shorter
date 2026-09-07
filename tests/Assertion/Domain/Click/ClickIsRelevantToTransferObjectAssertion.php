<?php

namespace Tests\Assertion\Domain\Click;

use Domain\Click\Entities\Click;
use Domain\Click\TransferObjects\NewClickTransferObject;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\ExpectationFailedException;

final class ClickIsRelevantToTransferObjectAssertion extends Constraint
{
    private ?ExpectationFailedException $expectationFailedException = null;

    public function __construct(
        private readonly NewClickTransferObject $transferObject,
    ) {
    }

    public function toString(): string
    {
        return 'The Click is relevant to the transfer object.';
    }

    protected function matches(mixed $other): bool
    {
        \assert($other instanceof Click);

        try {
            Assert::assertSame(
                $this->transferObject->id->toString(),
                $other->getId()->toString(),
                'Ids are not the same.',
            );
            Assert::assertSame(
                $this->transferObject->url->getId()->toString(),
                $other->getUrl()->getId()->toString(),
                'Urls are not the same.',
            );
        } catch (ExpectationFailedException $exception) {
            $this->expectationFailedException = $exception;

            return false;
        }

        return true;
    }

    protected function failureDescription(mixed $other): string
    {
        return sprintf('%s %s', $this->toString(), $this->expectationFailedException?->getMessage() ?? '');
    }
}

<?php

namespace Tests\Assertion\Domain\Url;

use Domain\Url\Entities\Url;
use Domain\Url\TransferObjects\NewUrlTransferObject;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\ExpectationFailedException;

final class UrlIsRelevantToTransferObjectAssertion extends Constraint
{
    private ?ExpectationFailedException $expectationFailedException = null;

    public function __construct(
        private readonly NewUrlTransferObject $transferObject,
    ) {
    }

    public function toString(): string
    {
        return 'The Url is relevant to the transfer object.';
    }

    protected function matches(mixed $other): bool
    {
        \assert($other instanceof Url);

        try {
            Assert::assertSame(
                $this->transferObject->id->toString(),
                $other->getId()->toString(),
                'Ids are not the same.',
            );
            Assert::assertSame(
                $this->transferObject->code,
                $other->getCode(),
                'Codes are not the same.',
            );
            Assert::assertSame(
                $this->transferObject->targetUrl,
                $other->getTargetUrl(),
                'Target urls are not the same.',
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

<?php

namespace Tests\Domain\Click\Entities;

use Domain\Click\Entities\Click;
use Domain\Click\TransferObjects\NewClickTransferObject;
use Domain\Url\Entities\Url;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\Uid\Uuid;
use Tests\Assertion\Domain\Click\ClickIsRelevantToTransferObjectAssertion;
use Tests\TestCases\UnitTestCase;

final class ClickTest extends UnitTestCase
{
    /**
     * @throws Exception
     */
    public function testClickMustBeCreatedByTransferObject(): void
    {
        $transferObject = $this->createTransferObject();

        $click = Click::createByTransferObject($transferObject);

        self::assertThat($click, new ClickIsRelevantToTransferObjectAssertion($transferObject));
    }

    /**
     * @throws Exception
     */
    private function createTransferObject(): NewClickTransferObject
    {
        $url = self::createStub(Url::class);
        $url->method('getId')->willReturn(Uuid::v7());

        return new NewClickTransferObject(Uuid::v7(), $url);
    }
}

<?php

namespace Tests\Domain\Url\Entities;

use Domain\Url\Entities\Url;
use Domain\Url\TransferObjects\NewUrlTransferObject;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Uid\Uuid;
use Tests\Assertion\Domain\Url\UrlIsRelevantToTransferObjectAssertion;
use Tests\TestCases\UnitTestCase;

final class UrlTest extends UnitTestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();
    }

    public function testUrlMustBeCreatedByTransferObject(): void
    {
        $transferObject = $this->createTransferObject();

        $url = Url::createByTransferObject($transferObject);

        self::assertThat($url, new UrlIsRelevantToTransferObjectAssertion($transferObject));
    }

    private function createTransferObject(): NewUrlTransferObject
    {
        return new NewUrlTransferObject(
            Uuid::v7(),
            $this->faker->lexify('??????????'),
            $this->faker->url(),
        );
    }
}

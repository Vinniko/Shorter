<?php

namespace Tests\Domain\Click\Commands\Handlers;

use Domain\Click\Commands\RecordClickCommand;
use Domain\Click\Repositories\ClickRepositoryInterface;
use Domain\Click\TransferObjects\NewClickTransferObject;
use Domain\Url\Entities\Url;
use Domain\Url\Repositories\UrlRepositoryInterface;
use Domain\Url\TransferObjects\NewUrlTransferObject;
use Faker\Factory;
use Faker\Generator;
use Infrastructure\Persistence\Repositories\Click\TestClickRepository;
use Infrastructure\Persistence\Repositories\Url\TestUrlRepository;
use Symfony\Component\Uid\Uuid;
use Tests\TestCases\FunctionalTestCase;

final class RecordClickCommandHandlerTest extends FunctionalTestCase
{
    private Generator $faker;

    public function testClickMustBeRecorded(): void
    {
        $url = $this->createAndSaveUrl();

        $this->getCommandBus()->dispatch($this->createCommand($url));

        $repository = $this->getServiceByInterface(
            ClickRepositoryInterface::class,
            TestClickRepository::class,
        );

        self::assertSame(1, $repository->countByUrl($url));
    }

    private function createCommand(Url $url): RecordClickCommand
    {
        $transferObject = new NewClickTransferObject(Uuid::v7(), $url);

        return new RecordClickCommand($transferObject);
    }

    private function createAndSaveUrl(): Url
    {
        $transferObject = new NewUrlTransferObject(
            Uuid::v7(),
            $this->faker->lexify('??????????'),
            $this->faker->url(),
        );

        $url = Url::createByTransferObject($transferObject);

        $this->getServiceByInterface(
            UrlRepositoryInterface::class,
            TestUrlRepository::class,
        )
            ->save($url);

        return $url;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();
    }
}

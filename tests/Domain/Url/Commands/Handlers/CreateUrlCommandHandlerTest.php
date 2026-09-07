<?php

namespace Tests\Domain\Url\Commands\Handlers;

use Domain\Url\Commands\CreateUrlCommand;
use Domain\Url\Entities\Url;
use Domain\Url\Repositories\UrlRepositoryInterface;
use Domain\Url\TransferObjects\NewUrlTransferObject;
use Faker\Factory;
use Faker\Generator;
use Infrastructure\Persistence\Repositories\Url\TestUrlRepository;
use Symfony\Component\Uid\Uuid;
use Tests\Assertion\Domain\Url\UrlIsRelevantToTransferObjectAssertion;
use Tests\TestCases\FunctionalTestCase;

final class CreateUrlCommandHandlerTest extends FunctionalTestCase
{
    private Generator $faker;

    public function testUrlMustBeCreated(): void
    {
        $command = $this->createCommand();

        $this->getCommandBus()->dispatch($command);

        $repository = $this->getServiceByInterface(
            UrlRepositoryInterface::class,
            TestUrlRepository::class,
        );

        $createdUrl = $repository->findByCode($command->transferObject->code);
        \assert($createdUrl instanceof Url);

        self::assertThat($createdUrl, new UrlIsRelevantToTransferObjectAssertion($command->transferObject));
    }

    private function createCommand(): CreateUrlCommand
    {
        $transferObject = new NewUrlTransferObject(
            Uuid::v7(),
            $this->faker->lexify('??????????'),
            $this->faker->url(),
        );

        return new CreateUrlCommand($transferObject);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();
    }
}

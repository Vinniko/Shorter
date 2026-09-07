<?php

namespace Tests\Application\Click\UseCases\Handlers;

use Application\Click\UseCases\RecordClickUseCase;
use Domain\Click\Commands\RecordClickCommand;
use Domain\Url\Entities\Url;
use Domain\Url\Exceptions\UrlNotFoundException;
use Domain\Url\Repositories\UrlRepositoryInterface;
use Domain\Url\TransferObjects\NewUrlTransferObject;
use Faker\Factory;
use Faker\Generator;
use Infrastructure\Persistence\Repositories\Url\TestUrlRepository;
use Symfony\Component\Uid\Uuid;
use Tests\TestCases\FunctionalTestCase;

final class RecordClickUseCaseHandlerTest extends FunctionalTestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();
    }

    public function testRecordClickCommandMustBeDispatched(): void
    {
        $url = $this->createAndSaveUrl();

        $useCase = new RecordClickUseCase();
        $useCase->code = $url->getCode();

        $this->getCommandBus()->dispatch($useCase);

        $this->getCommandBus()->assertIsDispatched(RecordClickCommand::class);
    }

    public function testExceptionMustBeThrownWhenUrlIsNotFound(): void
    {
        $useCase = new RecordClickUseCase();
        $useCase->code = $this->faker->lexify('??????????');

        $this->expectException(UrlNotFoundException::class);

        $this->getCommandBus()->dispatch($useCase);
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
}

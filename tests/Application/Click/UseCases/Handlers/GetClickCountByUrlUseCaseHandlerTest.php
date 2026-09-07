<?php

namespace Tests\Application\Click\UseCases\Handlers;

use Application\Click\UseCases\GetClickCountByUrlUseCase;
use Domain\Url\Entities\Url;
use Domain\Url\Exceptions\UrlNotFoundException;
use Domain\Url\Repositories\UrlRepositoryInterface;
use Domain\Url\TransferObjects\NewUrlTransferObject;
use Faker\Factory;
use Faker\Generator;
use Infrastructure\Persistence\Repositories\Url\TestUrlRepository;
use Symfony\Component\Uid\Uuid;
use Tests\TestCases\FunctionalTestCase;

final class GetClickCountByUrlUseCaseHandlerTest extends FunctionalTestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();
    }

    public function testClickCountMustBeReturned(): void
    {
        $url = $this->createAndSaveUrl();

        $useCase = new GetClickCountByUrlUseCase();
        $useCase->code = $url->getCode();

        $count = $this->getQueryBus()->dispatch($useCase);

        self::assertSame(0, $count);
    }

    public function testExceptionMustBeThrownWhenUrlIsNotFound(): void
    {
        $useCase = new GetClickCountByUrlUseCase();
        $useCase->code = $this->faker->lexify('??????????');

        $this->expectException(UrlNotFoundException::class);

        $this->getQueryBus()->dispatch($useCase);
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

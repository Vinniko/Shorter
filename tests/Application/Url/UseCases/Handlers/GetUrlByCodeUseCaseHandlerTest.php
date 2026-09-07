<?php

namespace Tests\Application\Url\UseCases\Handlers;

use Application\Url\UseCases\GetUrlByCodeUseCase;
use Domain\Url\Entities\Url;
use Domain\Url\Exceptions\UrlNotFoundException;
use Domain\Url\Repositories\UrlRepositoryInterface;
use Domain\Url\TransferObjects\NewUrlTransferObject;
use Faker\Factory;
use Faker\Generator;
use Infrastructure\Persistence\Repositories\Url\TestUrlRepository;
use Symfony\Component\Uid\Uuid;
use Tests\TestCases\FunctionalTestCase;

final class GetUrlByCodeUseCaseHandlerTest extends FunctionalTestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();
    }

    public function testUrlMustBeFound(): void
    {
        $url = $this->createAndSaveUrl();

        $useCase = new GetUrlByCodeUseCase();
        $useCase->code = $url->getCode();

        $foundUrl = $this->getQueryBus()->dispatch($useCase);

        self::assertInstanceOf(Url::class, $foundUrl);
        self::assertSame($url->getId()->toString(), $foundUrl->getId()->toString());
    }

    public function testExceptionMustBeThrownWhenUrlIsNotFound(): void
    {
        $useCase = new GetUrlByCodeUseCase();
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

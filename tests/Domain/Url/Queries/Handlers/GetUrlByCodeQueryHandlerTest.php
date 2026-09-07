<?php

namespace Tests\Domain\Url\Queries\Handlers;

use Domain\Url\Entities\Url;
use Domain\Url\Queries\GetUrlByCodeQuery;
use Domain\Url\Repositories\UrlRepositoryInterface;
use Faker\Factory;
use Faker\Generator;
use Infrastructure\Persistence\Repositories\Url\InMemoryUrlRepository;
use Infrastructure\Persistence\Repositories\Url\TestUrlRepository;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCases\FunctionalTestCase;

final class GetUrlByCodeQueryHandlerTest extends FunctionalTestCase
{
    private InMemoryUrlRepository $repository;

    private Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();
        $this->repository = $this->getServiceByClassName(InMemoryUrlRepository::class);

        $this->getServiceByInterface(
            UrlRepositoryInterface::class,
            TestUrlRepository::class,
        )
            ->useInMemory();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->getServiceByInterface(
            UrlRepositoryInterface::class,
            TestUrlRepository::class,
        )
            ->useReal();
    }

    public function testUrlMustBeFound(): void
    {
        $url = $this->createUrl('abc1234567');

        $query = new GetUrlByCodeQuery('abc1234567');

        $foundUrl = $this->getQueryBus()->dispatch($query);

        self::assertSame($url, $foundUrl);
    }

    public function testNullMustBeReturnedWhenUrlIsNotFound(): void
    {
        $query = new GetUrlByCodeQuery($this->faker->lexify('??????????'));

        $foundUrl = $this->getQueryBus()->dispatch($query);

        self::assertNull($foundUrl);
    }

    /**
     * @throws Exception
     */
    private function createUrl(string $code): Url
    {
        $url = self::createStub(Url::class);
        $url->method('getCode')->willReturn($code);

        $this->repository->save($url);

        return $url;
    }
}

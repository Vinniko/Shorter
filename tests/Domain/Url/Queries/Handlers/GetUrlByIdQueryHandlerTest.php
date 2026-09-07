<?php

namespace Tests\Domain\Url\Queries\Handlers;

use Domain\Url\Entities\Url;
use Domain\Url\Exceptions\UrlNotFoundException;
use Domain\Url\Queries\GetUrlByIdQuery;
use Domain\Url\Repositories\UrlRepositoryInterface;
use Infrastructure\Persistence\Repositories\Url\InMemoryUrlRepository;
use Infrastructure\Persistence\Repositories\Url\TestUrlRepository;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\Uid\Uuid;
use Tests\TestCases\FunctionalTestCase;

final class GetUrlByIdQueryHandlerTest extends FunctionalTestCase
{
    private InMemoryUrlRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

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
        $id = Uuid::v7();
        $url = $this->createUrl($id);

        $query = new GetUrlByIdQuery($id);

        $foundUrl = $this->getQueryBus()->dispatch($query);

        self::assertSame($url, $foundUrl);
    }

    public function testExceptionMustBeThrownWhenUrlIsNotFound(): void
    {
        $query = new GetUrlByIdQuery(Uuid::v7());

        $this->expectException(UrlNotFoundException::class);

        $this->getQueryBus()->dispatch($query);
    }

    /**
     * @throws Exception
     */
    private function createUrl(Uuid $id): Url
    {
        $url = self::createStub(Url::class);
        $url->method('getId')->willReturn($id);

        $this->repository->save($url);

        return $url;
    }
}

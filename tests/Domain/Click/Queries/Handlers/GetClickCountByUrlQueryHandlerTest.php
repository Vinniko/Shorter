<?php

namespace Tests\Domain\Click\Queries\Handlers;

use Domain\Click\Entities\Click;
use Domain\Click\Queries\GetClickCountByUrlQuery;
use Domain\Click\Repositories\ClickRepositoryInterface;
use Domain\Url\Entities\Url;
use Infrastructure\Persistence\Repositories\Click\InMemoryClickRepository;
use Infrastructure\Persistence\Repositories\Click\TestClickRepository;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\Uid\Uuid;
use Tests\TestCases\FunctionalTestCase;

final class GetClickCountByUrlQueryHandlerTest extends FunctionalTestCase
{
    private InMemoryClickRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->getServiceByClassName(InMemoryClickRepository::class);

        $this->getServiceByInterface(
            ClickRepositoryInterface::class,
            TestClickRepository::class,
        )
            ->useInMemory();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->getServiceByInterface(
            ClickRepositoryInterface::class,
            TestClickRepository::class,
        )
            ->useReal();
    }

    /**
     * @throws Exception
     */
    public function testClickCountMustBeReturned(): void
    {
        $url = $this->createUrl();

        $this->saveClick($url);
        $this->saveClick($url);
        $this->saveClick($this->createUrl());

        $query = new GetClickCountByUrlQuery($url);

        $count = $this->getQueryBus()->dispatch($query);

        self::assertSame(2, $count);
    }

    /**
     * @throws Exception
     */
    private function createUrl(): Url
    {
        $url = self::createStub(Url::class);
        $url->method('getId')->willReturn(Uuid::v7());

        return $url;
    }

    /**
     * @throws Exception
     */
    private function saveClick(Url $url): void
    {
        $click = self::createStub(Click::class);
        $click->method('getId')->willReturn(Uuid::v7());
        $click->method('getUrl')->willReturn($url);

        $this->repository->save($click);
    }
}

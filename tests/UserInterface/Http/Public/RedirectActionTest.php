<?php

namespace Tests\UserInterface\Http\Public;

use Domain\Url\Entities\Url;
use Domain\Url\Repositories\UrlRepositoryInterface;
use Domain\Url\TransferObjects\NewUrlTransferObject;
use Faker\Factory;
use Faker\Generator;
use Infrastructure\Persistence\Repositories\Url\TestUrlRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

final class RedirectActionTest extends PublicTestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();
    }

    public function testRedirectMustHappen(): void
    {
        $url = $this->createAndSaveUrl();

        $this->client->request('GET', sprintf('/%s', $url->getCode()));

        $response = $this->client->getResponse();

        self::assertSame(Response::HTTP_FOUND, $response->getStatusCode());
        self::assertSame($url->getTargetUrl(), $response->headers->get('Location'));
    }

    public function testNotFoundMustBeReturnedForUnknownCode(): void
    {
        $this->client->request('GET', '/AAAAAAAAAA');

        self::assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
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

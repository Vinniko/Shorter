<?php

namespace Tests\UserInterface\Http\Api\Url;

use Domain\Url\Entities\Url;
use Domain\Url\Repositories\UrlRepositoryInterface;
use Domain\Url\TransferObjects\NewUrlTransferObject;
use Faker\Factory;
use Faker\Generator;
use Infrastructure\Persistence\Repositories\Url\TestUrlRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;
use Tests\UserInterface\Http\Api\ApiTestCase;

final class GetLinkStatsActionTest extends ApiTestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();
    }

    public function testStatsMustBeReturned(): void
    {
        $url = $this->createAndSaveUrl();

        $this->client->request('GET', sprintf('/urls/%s/stats', $url->getCode()));

        $response = $this->client->getResponse();

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());

        $data = json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame($url->getCode(), $data['url']['code']);
        self::assertSame($url->getTargetUrl(), $data['url']['target_url']);
        self::assertSame(0, $data['click_qty']);
    }

    public function testNotFoundMustBeReturnedForUnknownCode(): void
    {
        $this->client->request('GET', '/urls/AAAAAAAAAA/stats');

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

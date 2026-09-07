<?php

namespace Tests\Application\View\Url;

use Application\View\Url\LinkStatsViewFactory;
use Domain\Url\Entities\Url;
use Domain\Url\TransferObjects\NewUrlTransferObject;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Uid\Uuid;
use Tests\TestCases\FunctionalTestCase;

final class LinkStatsViewFactoryTest extends FunctionalTestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();
    }

    public function testViewMustBeCreatedFromUrlAndClickQty(): void
    {
        $url = $this->createUrl();

        $view = $this->getServiceByClassName(LinkStatsViewFactory::class)->create($url, 5);

        self::assertSame($url->getCode(), $view->url->code);
        self::assertSame(5, $view->click_qty);
    }

    private function createUrl(): Url
    {
        $transferObject = new NewUrlTransferObject(
            Uuid::v7(),
            $this->faker->lexify('??????????'),
            $this->faker->url(),
        );

        return Url::createByTransferObject($transferObject);
    }
}

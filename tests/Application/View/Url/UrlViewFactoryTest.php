<?php

namespace Tests\Application\View\Url;

use Application\View\Url\UrlViewFactory;
use Domain\Url\Entities\Url;
use Domain\Url\TransferObjects\NewUrlTransferObject;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Uid\Uuid;
use Tests\TestCases\FunctionalTestCase;

final class UrlViewFactoryTest extends FunctionalTestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();
    }

    public function testViewMustBeCreatedFromUrl(): void
    {
        $url = $this->createUrl();

        $view = $this->getServiceByClassName(UrlViewFactory::class)->create($url);

        self::assertSame($url->getId()->toString(), $view->id);
        self::assertSame($url->getCode(), $view->code);
        self::assertSame($url->getTargetUrl(), $view->target_url);
        self::assertStringEndsWith('/'.$url->getCode(), $view->short_url);
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

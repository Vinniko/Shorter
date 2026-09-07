<?php

namespace Tests\Domain\Url\TransferObjects\Factories;

use Domain\Url\Entities\Url;
use Domain\Url\Repositories\UrlRepositoryInterface;
use Domain\Url\TransferObjects\Factories\NewUrlTransferObjectFactory;
use Domain\Url\TransferObjects\NewUrlTransferObject;
use Domain\Utils\ShortCode\ShortCodeGeneratorInterface;
use Faker\Factory;
use Faker\Generator;
use Infrastructure\Persistence\Repositories\Url\InMemoryUrlRepository;
use Infrastructure\Persistence\Repositories\Url\TestUrlRepository;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\Uid\Uuid;
use Tests\TestCases\FunctionalTestCase;

final class NewUrlTransferObjectFactoryTest extends FunctionalTestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();

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

    public function testTransferObjectMustBeCreatedForIdAndTargetUrl(): void
    {
        $id = Uuid::v7();
        $targetUrl = $this->faker->url();

        $transferObject = $this->getFactory()->createForIdAndTargetUrl($id, $targetUrl);

        self::assertSame($id, $transferObject->id);
        self::assertSame($targetUrl, $transferObject->targetUrl);
        self::assertSame(10, strlen($transferObject->code));
    }

    /**
     * @throws Exception
     */
    public function testCodeMustBeRegeneratedWhenItAlreadyExists(): void
    {
        $repository = $this->getServiceByClassName(InMemoryUrlRepository::class);
        $repository->save($this->createUrl('AAAAAAAAAA'));

        $generator = self::createStub(ShortCodeGeneratorInterface::class);
        $generator->method('generate')->willReturnCallback(static function (): string {
            static $codes = ['AAAAAAAAAA', 'BBBBBBBBBB'];

            return array_shift($codes);
        });

        self::getContainer()->set(ShortCodeGeneratorInterface::class, $generator);

        $transferObject = $this->getFactory()->createForIdAndTargetUrl(Uuid::v7(), $this->faker->url());

        self::assertSame('BBBBBBBBBB', $transferObject->code);
    }

    private function getFactory(): NewUrlTransferObjectFactory
    {
        return $this->getServiceByClassName(NewUrlTransferObjectFactory::class);
    }

    private function createUrl(string $code): Url
    {
        $transferObject = new NewUrlTransferObject(Uuid::v7(), $code, $this->faker->url());

        return Url::createByTransferObject($transferObject);
    }
}

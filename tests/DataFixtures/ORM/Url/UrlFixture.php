<?php

namespace Tests\DataFixtures\ORM\Url;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Tests\Builders\ORM\Url\UrlBuilder;

final class UrlFixture extends Fixture
{
    public function __construct(
        private readonly UrlBuilder $builder,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $url = $this->builder
            ->create()
            ->save()
            ->getAndReset();

        $this->addReference(self::getReferenceName(), $url);

        $manager->persist($url);
        $manager->flush();
    }

    public static function getReferenceName(): string
    {
        return 'url-fixture';
    }
}

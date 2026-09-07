<?php

namespace Tests\DataFixtures\ORM\Click;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Tests\Builders\ORM\Click\ClickBuilder;

final class ClickFixture extends Fixture
{
    public function __construct(
        private readonly ClickBuilder $builder,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $click = $this->builder
            ->create()
            ->save()
            ->getAndReset();

        $this->addReference(self::getReferenceName(), $click);

        $manager->persist($click);
        $manager->flush();
    }

    public static function getReferenceName(): string
    {
        return 'click-fixture';
    }
}

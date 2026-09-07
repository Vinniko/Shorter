<?php

namespace Tests\TestCases;

use Doctrine\Persistence\ObjectManager;
use Domain\MessageBus\CommandBusInterface;
use Domain\MessageBus\QueryBusInterface;
use Infrastructure\MessageBus\TestCommandBus;
use Infrastructure\MessageBus\TestQueryBus;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class FunctionalTestCase extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        static::bootKernel();
    }

    protected function tearDown(): void
    {
        self::ensureKernelShutdown();

        parent::tearDown();
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $serviceClassName
     *
     * @return T
     */
    final protected function getServiceByClassName(string $serviceClassName): object
    {
        return $this->getServiceByInterface($serviceClassName, $serviceClassName);
    }

    /**
     * @template TInterfaceClassName of object
     * @template TExpectedServiceClassName of object
     *
     * @param class-string<TInterfaceClassName> $interfaceClassName
     * @param class-string<TExpectedServiceClassName> $expectedServiceClassName
     *
     * @return TExpectedServiceClassName
     */
    final protected function getServiceByInterface(string $interfaceClassName, string $expectedServiceClassName): object
    {
        $service = self::getContainer()->get($interfaceClassName);

        \assert($service instanceof $expectedServiceClassName);

        return $service;
    }

    final protected function getObjectManager(): ObjectManager
    {
        return self::getContainer()->get('doctrine.orm.default_entity_manager');
    }

    final protected function getCommandBus(): TestCommandBus
    {
        return $this->getServiceByInterface(CommandBusInterface::class, TestCommandBus::class);
    }

    final protected function getQueryBus(): TestQueryBus
    {
        return $this->getServiceByInterface(QueryBusInterface::class, TestQueryBus::class);
    }
}

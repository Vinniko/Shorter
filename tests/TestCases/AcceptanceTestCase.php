<?php

namespace Tests\TestCases;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AcceptanceTestCase extends WebTestCase
{
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
}

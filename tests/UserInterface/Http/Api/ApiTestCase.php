<?php

namespace Tests\UserInterface\Http\Api;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Tests\TestCases\AcceptanceTestCase;

abstract class ApiTestCase extends AcceptanceTestCase
{
    protected KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->client->setServerParameter('HTTP_HOST', (string) static::getContainer()->getParameter('api_domain'));
    }
}

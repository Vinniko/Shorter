<?php

namespace Tests\UserInterface\Http\Api\Url;

use Symfony\Component\HttpFoundation\Response;
use Tests\UserInterface\Http\Api\ApiTestCase;

final class CreateUrlActionTest extends ApiTestCase
{
    public function testUrlMustBeCreated(): void
    {
        $this->client->request(
            'POST',
            '/urls',
            content: json_encode(['target_url' => 'https://example.com/some/path'], JSON_THROW_ON_ERROR),
            server: ['CONTENT_TYPE' => 'application/json'],
        );

        $response = $this->client->getResponse();

        self::assertSame(Response::HTTP_CREATED, $response->getStatusCode());

        $data = json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame('https://example.com/some/path', $data['target_url']);
        self::assertSame(10, strlen($data['code']));
        self::assertStringEndsWith('/'.$data['code'], $data['short_url']);
    }

    public function testValidationFailedResponseMustBeReturnedForInvalidTargetUrl(): void
    {
        $this->client->request(
            'POST',
            '/urls',
            content: json_encode(['target_url' => 'not-a-url'], JSON_THROW_ON_ERROR),
            server: ['CONTENT_TYPE' => 'application/json'],
        );

        self::assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());
    }
}

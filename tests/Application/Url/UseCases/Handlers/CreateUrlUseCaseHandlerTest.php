<?php

namespace Tests\Application\Url\UseCases\Handlers;

use Application\Url\UseCases\CreateUrlUseCase;
use Domain\Url\Commands\CreateUrlCommand;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Uid\Uuid;
use Tests\TestCases\FunctionalTestCase;

final class CreateUrlUseCaseHandlerTest extends FunctionalTestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();
    }

    public function testCreateUrlCommandMustBeDispatched(): void
    {
        $useCase = $this->createUseCase($this->faker->url());

        $this->getCommandBus()->dispatch($useCase);

        $this->getCommandBus()->assertIsDispatched(CreateUrlCommand::class);
    }

    public function testValidationMustFailForInvalidTargetUrl(): void
    {
        $useCase = $this->createUseCase('not-a-valid-url');

        $this->expectException(ValidationFailedException::class);

        $this->getCommandBus()->dispatch($useCase);
    }

    public function testValidationMustFailForBlankTargetUrl(): void
    {
        $useCase = $this->createUseCase('');

        $this->expectException(ValidationFailedException::class);

        $this->getCommandBus()->dispatch($useCase);
    }

    private function createUseCase(string $targetUrl): CreateUrlUseCase
    {
        $useCase = new CreateUrlUseCase();
        $useCase->id = Uuid::v7();
        $useCase->targetUrl = $targetUrl;

        return $useCase;
    }
}

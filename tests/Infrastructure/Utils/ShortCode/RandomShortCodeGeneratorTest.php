<?php

namespace Tests\Infrastructure\Utils\ShortCode;

use Domain\Utils\ShortCode\ShortCodeGeneratorInterface;
use Infrastructure\Utils\ShortCode\RandomShortCodeGenerator;
use Tests\TestCases\FunctionalTestCase;

final class RandomShortCodeGeneratorTest extends FunctionalTestCase
{
    private RandomShortCodeGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->generator = $this->getServiceByInterface(
            ShortCodeGeneratorInterface::class,
            RandomShortCodeGenerator::class,
        );
    }

    public function testCodeMustBeTenCharactersLong(): void
    {
        self::assertSame(10, strlen($this->generator->generate()));
    }

    public function testCodeMustOnlyContainAlphanumericCharacters(): void
    {
        self::assertMatchesRegularExpression('/^[A-Za-z0-9]{10}$/', $this->generator->generate());
    }

    public function testGeneratedCodesMustNotAllBeTheSame(): void
    {
        $codes = [];

        for ($i = 0; $i < 50; $i++) {
            $codes[] = $this->generator->generate();
        }

        self::assertGreaterThan(1, count(array_unique($codes)));
    }
}

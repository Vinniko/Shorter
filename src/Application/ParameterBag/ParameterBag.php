<?php

namespace Application\ParameterBag;

use Throwable;

/**
 * @phpstan-template-covariant T as array
 */
final readonly class ParameterBag
{
    /** @param T $parameters */
    public function __construct(
        /** @var T */
        private array $parameters,
    ) {}

    /**
     * @return self<array<mixed>>
     */
    public static function createFromJson(string $json): self
    {
        try {
            $payload = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            $payload = [];
        }

        return new self(is_array($payload) ? $payload : []);
    }

    public function get(string $parameter, mixed $default = null): mixed
    {
        return $this->parameters[$parameter] ?? $default;
    }

    public function getString(string $parameter, string $default = ''): string
    {
        try {
            $value = trim((string) $this->get($parameter, $default));
        } catch (Throwable) {
            $value = $default;
        }

        return $value;
    }
}

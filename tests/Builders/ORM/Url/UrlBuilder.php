<?php

namespace Tests\Builders\ORM\Url;

use Domain\Url\Entities\Url;
use Domain\Url\Repositories\UrlRepositoryInterface;
use Domain\Url\TransferObjects\NewUrlTransferObject;
use Faker\Factory;
use Symfony\Component\Uid\Uuid;
use Tests\Builders\ORM\Assertion\EntityAttributesAssertion;
use Tests\Builders\ORM\Assertion\EntityBuildProcessHasAlreadyStartedAssertion;
use Tests\Builders\ORM\Assertion\EntityBuildProcessHasNotStartedYetAssertion;
use Tests\Builders\ORM\Exception\EntityAttributeNotAllowedException;
use Tests\Builders\ORM\Exception\EntityBuildProcessHasAlreadyStartedException;
use Tests\Builders\ORM\Exception\EntityBuildProcessHasNotStartedYetException;

/**
 * @phpstan-type UrlCustomAttributes = array{
 *     id?: Uuid,
 *     code?: string,
 *     targetUrl?: string,
 * }
 * @phpstan-type UrlCompiledAttributes = array{
 *      id: Uuid,
 *      code: string,
 *      targetUrl: string,
 *  }
 */
final class UrlBuilder
{
    private ?Url $url = null;

    public function __construct(
        private readonly UrlRepositoryInterface $repository,
    ) {
    }

    /**
     * @param UrlCustomAttributes $attributes
     *
     * @throws EntityAttributeNotAllowedException
     * @throws EntityBuildProcessHasAlreadyStartedException
     */
    public function create(array $attributes = []): self
    {
        EntityBuildProcessHasNotStartedYetAssertion::assert($this->url);
        \assert($this->url === null);

        $compiledAttributes = $this->compileAttributes($attributes);
        $this->url = $this->createUrl($compiledAttributes);

        return $this;
    }

    /**
     * @throws EntityBuildProcessHasNotStartedYetException
     */
    public function save(): self
    {
        EntityBuildProcessHasAlreadyStartedAssertion::assert(Url::class, $this->url);
        \assert($this->url instanceof Url);

        $this->repository->save($this->url);

        return $this;
    }

    /**
     * @throws EntityBuildProcessHasNotStartedYetException
     */
    public function getAndReset(): Url
    {
        EntityBuildProcessHasAlreadyStartedAssertion::assert(Url::class, $this->url);
        \assert($this->url instanceof Url);

        $url = $this->url;

        $this->reset();

        return $url;
    }

    /**
     * @param UrlCustomAttributes $attributes
     *
     * @return UrlCompiledAttributes
     *
     * @throws EntityAttributeNotAllowedException
     */
    private function compileAttributes(array $attributes): array
    {
        $mergedAttributes = array_merge($this->getRandomAttributes(), $attributes);

        EntityAttributesAssertion::assert($mergedAttributes, $this->getRandomAttributes());

        return $mergedAttributes;
    }

    /**
     * @return UrlCompiledAttributes
     */
    private function getRandomAttributes(): array
    {
        $faker = Factory::create();

        return [
            'id' => Uuid::v7(),
            'code' => $faker->lexify('??????????'),
            'targetUrl' => $faker->url(),
        ];
    }

    /**
     * @param UrlCompiledAttributes $attributes
     */
    private function createUrl(array $attributes): Url
    {
        $transferObject = new NewUrlTransferObject(
            $attributes['id'],
            $attributes['code'],
            $attributes['targetUrl'],
        );

        return Url::createByTransferObject($transferObject);
    }

    private function reset(): void
    {
        $this->url = null;
    }
}

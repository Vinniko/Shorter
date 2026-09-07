<?php

namespace Tests\Builders\ORM\Click;

use Domain\Click\Entities\Click;
use Domain\Click\Repositories\ClickRepositoryInterface;
use Domain\Click\TransferObjects\NewClickTransferObject;
use Domain\Url\Entities\Url;
use Symfony\Component\Uid\Uuid;
use Tests\Builders\ORM\Assertion\EntityAttributesAssertion;
use Tests\Builders\ORM\Assertion\EntityBuildProcessHasAlreadyStartedAssertion;
use Tests\Builders\ORM\Assertion\EntityBuildProcessHasNotStartedYetAssertion;
use Tests\Builders\ORM\Exception\EntityAttributeNotAllowedException;
use Tests\Builders\ORM\Exception\EntityBuildProcessHasAlreadyStartedException;
use Tests\Builders\ORM\Exception\EntityBuildProcessHasNotStartedYetException;
use Tests\Builders\ORM\Url\UrlBuilder;

/**
 * @phpstan-type ClickCustomAttributes = array{
 *     id?: Uuid,
 *     url?: Url,
 * }
 * @phpstan-type ClickCompiledAttributes = array{
 *      id: Uuid,
 *      url: Url,
 *  }
 */
final class ClickBuilder
{
    private ?Click $click = null;

    public function __construct(
        private readonly ClickRepositoryInterface $repository,
        private readonly UrlBuilder $urlBuilder,
    ) {
    }

    /**
     * @param ClickCustomAttributes $attributes
     *
     * @throws EntityAttributeNotAllowedException
     * @throws EntityBuildProcessHasAlreadyStartedException
     */
    public function create(array $attributes = []): self
    {
        EntityBuildProcessHasNotStartedYetAssertion::assert($this->click);
        \assert($this->click === null);

        $compiledAttributes = $this->compileAttributes($attributes);
        $this->click = $this->createClick($compiledAttributes);

        return $this;
    }

    /**
     * @throws EntityBuildProcessHasNotStartedYetException
     */
    public function save(): self
    {
        EntityBuildProcessHasAlreadyStartedAssertion::assert(Click::class, $this->click);
        \assert($this->click instanceof Click);

        $this->repository->save($this->click);

        return $this;
    }

    /**
     * @throws EntityBuildProcessHasNotStartedYetException
     */
    public function getAndReset(): Click
    {
        EntityBuildProcessHasAlreadyStartedAssertion::assert(Click::class, $this->click);
        \assert($this->click instanceof Click);

        $click = $this->click;

        $this->reset();

        return $click;
    }

    /**
     * @param ClickCustomAttributes $attributes
     *
     * @return ClickCompiledAttributes
     *
     * @throws EntityAttributeNotAllowedException
     */
    private function compileAttributes(array $attributes): array
    {
        $randomAttributes = $this->getRandomAttributes();
        $mergedAttributes = array_merge($randomAttributes, $attributes);

        EntityAttributesAssertion::assert($mergedAttributes, $randomAttributes);

        return $mergedAttributes;
    }

    /**
     * @return ClickCompiledAttributes
     */
    private function getRandomAttributes(): array
    {
        return [
            'id' => Uuid::v7(),
            'url' => $this->urlBuilder->create()->save()->getAndReset(),
        ];
    }

    /**
     * @param ClickCompiledAttributes $attributes
     */
    private function createClick(array $attributes): Click
    {
        $transferObject = new NewClickTransferObject(
            $attributes['id'],
            $attributes['url'],
        );

        return Click::createByTransferObject($transferObject);
    }

    private function reset(): void
    {
        $this->click = null;
    }
}

<?php

namespace Domain\Url\TransferObjects\Factories;

use Domain\Url\Repositories\UrlRepositoryInterface;
use Domain\Url\TransferObjects\NewUrlTransferObject;
use Domain\Utils\ShortCode\ShortCodeGeneratorInterface;
use Symfony\Component\Uid\Uuid;

final readonly class NewUrlTransferObjectFactory
{
    public function __construct(
        private UrlRepositoryInterface $urlRepository,
        private ShortCodeGeneratorInterface $shortCodeGenerator,
    ) {}

    public function createForIdAndTargetUrl(Uuid $id, string $targetUrl): NewUrlTransferObject
    {
        return new NewUrlTransferObject(
            $id,
            $this->generateUniqueCode(),
            $targetUrl,
        );
    }

    private function generateUniqueCode(): string
    {
        do {
            $code = $this->shortCodeGenerator->generate();
        } while ($this->urlRepository->findByCode($code) !== null);

        return $code;
    }
}

<?php

namespace Domain\Url\Commands\Handlers;

use Domain\Url\Commands\CreateUrlCommand;
use Domain\Url\Entities\Url;
use Domain\Url\Repositories\UrlRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreateUrlCommandHandler
{
    public function __construct(
        private UrlRepositoryInterface $urlRepository,
    ) {}

    public function __invoke(CreateUrlCommand $command): void
    {
        $url = Url::createByTransferObject($command->transferObject);

        $this->urlRepository->save($url);
    }
}

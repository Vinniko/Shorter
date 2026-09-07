<?php

namespace Domain\Url\Queries\Handlers;

use Domain\Url\Entities\Url;
use Domain\Url\Exceptions\UrlNotFoundException;
use Domain\Url\Queries\GetUrlByIdQuery;
use Domain\Url\Repositories\UrlRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetUrlByIdQueryHandler
{
    public function __construct(
        private UrlRepositoryInterface $urlRepository,
    ) {}

    public function __invoke(GetUrlByIdQuery $query): Url
    {
        $url = $this->urlRepository->findById($query->id);

        if ($url instanceof Url) {
            return $url;
        }

        throw UrlNotFoundException::withId($query->id);
    }
}

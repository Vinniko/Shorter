<?php

namespace Application\Url\UseCases;

use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateUrlUseCase
{
    #[Assert\NotBlank(message: 'The target URL is required.')]
    #[Assert\Url(message: 'The target URL is not a valid URL.', requireTld: true)]
    #[Assert\Length(max: 254)]
    #[OA\Property(
        property: 'target_url',
        description: 'The original, long URL to shorten.',
        type: 'string',
        example: 'https://example.com/some/very/long/path?with=query&params=here',
    )]
    public string $targetUrl;

    #[Ignore]
    public Uuid $id;
}

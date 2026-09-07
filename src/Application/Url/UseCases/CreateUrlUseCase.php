<?php

namespace Application\Url\UseCases;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateUrlUseCase
{
    #[Assert\NotBlank(message: 'The target URL is required.')]
    #[Assert\Url(message: 'The target URL is not a valid URL.', requireTld: true)]
    #[Assert\Length(max: 254)]
    public string $targetUrl;

    public Uuid $id;
}

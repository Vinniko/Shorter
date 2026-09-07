<?php

namespace Application\Url\UseCases;

use Symfony\Component\Validator\Constraints as Assert;

final class GetUrlByCodeUseCase
{
    #[Assert\NotBlank(message: 'The code is required.')]
    public string $code;
}

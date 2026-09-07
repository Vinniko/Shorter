<?php

namespace Application\Url\UseCases;

use Symfony\Component\Validator\Constraints as Assert;

final class GetUrlByIdUseCase
{
    #[Assert\NotBlank(message: 'The id is required.')]
    #[Assert\Uuid(message: 'The id is not a valid UUID.')]
    public string $id;
}

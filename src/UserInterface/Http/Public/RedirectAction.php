<?php

namespace UserInterface\Http\Public;

use Application\Click\UseCases\RecordClickUseCase;
use Application\Url\UseCases\GetUrlByCodeUseCase;
use Domain\MessageBus\CommandBusInterface;
use Domain\MessageBus\QueryBusInterface;
use Domain\Url\Entities\Url;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '/{code}',
    name: 'redirect',
    methods: ['GET'],
    requirements: ['code' => '[A-Za-z0-9]{10}'],
)]
#[OA\Tag(name: 'Redirect')]
#[OA\Response(response: Response::HTTP_FOUND, description: 'Redirected to the target URL.')]
#[OA\Response(response: Response::HTTP_NOT_FOUND, description: 'Short link not found.')]
final class RedirectAction extends AbstractController
{
    public function __construct(
        private readonly QueryBusInterface $queryBus,
        private readonly CommandBusInterface $commandBus,
    ) {}

    public function __invoke(string $code): RedirectResponse
    {
        $url = $this->getUrlByCode($code);

        $this->recordClick($code);

        return new RedirectResponse($url->getTargetUrl());
    }

    private function getUrlByCode(string $code): Url
    {
        $useCase = new GetUrlByCodeUseCase();
        $useCase->code = $code;

        return $this->queryBus->dispatch($useCase);
    }

    private function recordClick(string $code): void
    {
        $useCase = new RecordClickUseCase();
        $useCase->code = $code;

        $this->commandBus->dispatch($useCase);
    }
}

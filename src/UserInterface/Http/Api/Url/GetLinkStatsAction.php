<?php

namespace UserInterface\Http\Api\Url;

use Application\Click\UseCases\GetClickCountByUrlUseCase;
use Application\OpenApi\View\ViewObjectContent;
use Application\Url\UseCases\GetUrlByCodeUseCase;
use Application\View\Url\LinkStatsView;
use Application\View\Url\LinkStatsViewFactory;
use Domain\MessageBus\QueryBusInterface;
use Domain\Url\Entities\Url;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use UserInterface\Http\Api\AbstractApiController;

#[Route(
    path: '/urls/{code}/stats',
    name: 'api_get_link_stats',
    methods: ['GET'],
    requirements: ['code' => '[A-Za-z0-9]{10}'],
)]
#[OA\Tag(name: 'Urls')]
#[OA\Response(
    response: Response::HTTP_OK,
    description: 'Link statistics.',
    content: new ViewObjectContent(LinkStatsView::class),
)]
#[OA\Response(response: Response::HTTP_NOT_FOUND, description: 'Short link not found.')]
final class GetLinkStatsAction extends AbstractApiController
{
    public function __construct(
        private readonly QueryBusInterface $queryBus,
        private readonly LinkStatsViewFactory $viewFactory,
    ) {}

    public function __invoke(string $code): JsonResponse
    {
        $url = $this->getUrlByCode($code);
        $clickQty = $this->getClickQtyByCode($code);

        return new JsonResponse($this->viewFactory->create($url, $clickQty), Response::HTTP_OK);
    }

    private function getUrlByCode(string $code): Url
    {
        $useCase = new GetUrlByCodeUseCase();
        $useCase->code = $code;

        return $this->queryBus->dispatch($useCase);
    }

    private function getClickQtyByCode(string $code): int
    {
        $useCase = new GetClickCountByUrlUseCase();
        $useCase->code = $code;

        return $this->queryBus->dispatch($useCase);
    }
}

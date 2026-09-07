<?php

namespace Application\View\Url;

use Domain\Url\Entities\Url;

final readonly class LinkStatsViewFactory
{
    public function __construct(
        private UrlViewFactory $urlViewFactory,
    ) {}

    public function create(Url $url, int $clickQty): LinkStatsView
    {
        $view = new LinkStatsView();

        $view->url = $this->urlViewFactory->create($url);
        $view->click_qty = $clickQty;

        return $view;
    }
}

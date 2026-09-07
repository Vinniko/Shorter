<?php

namespace Application\View\Url;

use Domain\Url\Entities\Url;

final readonly class UrlViewFactory
{
    public function __construct(
        private string $publicUri,
    ) {}

    public function create(Url $url): UrlView
    {
        $view = new UrlView();

        $view->id = $url->getId()->toString();
        $view->code = $url->getCode();
        $view->target_url = $url->getTargetUrl();
        $view->short_url = rtrim($this->publicUri, '/').'/'.$url->getCode();

        return $view;
    }
}

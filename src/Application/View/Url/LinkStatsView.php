<?php

namespace Application\View\Url;

use Application\OpenApi\LinkStats as UseUrlOpenApi;

final class LinkStatsView
{
    public UrlView $url;

    #[UseUrlOpenApi\UrlClickQtyProperty]
    public int $click_qty;
}

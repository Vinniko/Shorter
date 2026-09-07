<?php

namespace Application\View\Url;

use Application\OpenApi\Url as UseUrlOpenApi;

class UrlView
{
    #[UseUrlOpenApi\UrlIdProperty]
    public string $id;

    #[UseUrlOpenApi\UrlCodeProperty]
    public string $code;

    #[UseUrlOpenApi\UrlTargetUrlProperty]
    public string $target_url;

    #[UseUrlOpenApi\UrlShortUrlProperty]
    public string $short_url;
}

<?php

namespace SlackPhp\Framework\Tests\Integration\Apps;

use SlackPhp\Framework\BaseApp;
use SlackPhp\Framework\Context;
use SlackPhp\Framework\Router;

class AnyApp extends BaseApp
{
    protected function prepareRouter(Router $router): void
    {
        $router->any(fn (Context $ctx) => $ctx->ack('hello'));
    }
}

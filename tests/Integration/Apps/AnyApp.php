<?php

namespace BubbaOps\Framework\Tests\Integration\Apps;

use BubbaOps\Framework\BaseApp;
use BubbaOps\Framework\Context;
use BubbaOps\Framework\Router;

class AnyApp extends BaseApp
{
    protected function prepareRouter(Router $router): void
    {
        $router->any(fn (Context $ctx) => $ctx->ack('hello'));
    }
}

<?php

declare(strict_types=1);

namespace SlackPhp\Framework\Listeners;

use SlackPhp\Framework\Context;
use SlackPhp\Framework\Interceptor;
use SlackPhp\Framework\Listener;

class Intercepted implements Listener
{
    private Interceptor $interceptor;

    private Listener $listener;

    public function __construct(Interceptor $interceptor, Listener $listener)
    {
        $this->interceptor = $interceptor;
        $this->listener = $listener;
    }

    public function handle(Context $context): void
    {
        $this->interceptor->intercept($context, $this->listener);
    }
}

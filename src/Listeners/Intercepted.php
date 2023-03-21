<?php

declare(strict_types=1);

namespace BubbaOps\Framework\Listeners;

use BubbaOps\Framework\Context;
use BubbaOps\Framework\Interceptor;
use BubbaOps\Framework\Listener;

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

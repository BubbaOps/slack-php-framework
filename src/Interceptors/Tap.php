<?php

declare(strict_types=1);

namespace BubbaOps\Framework\Interceptors;

use Closure;
use BubbaOps\Framework\Context;
use BubbaOps\Framework\Interceptor;
use BubbaOps\Framework\Listener;

/**
 * Interceptor that lets you tap into the context before the listener is executed.
 */
class Tap implements Interceptor
{
    private Closure $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback instanceof Closure ? $callback : Closure::fromCallable($callback);
    }

    public function intercept(Context $context, Listener $listener): void
    {
        ($this->callback)($context);
        $listener->handle($context);
    }
}

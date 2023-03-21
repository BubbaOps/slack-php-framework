<?php

declare(strict_types=1);

namespace BubbaOps\Framework\Interceptors;

use Closure;
use BubbaOps\Framework\Context;
use BubbaOps\Framework\Interceptor;
use BubbaOps\Framework\Listener;

/**
 * Lazily creates an interceptor at the time that it needs to be executed.
 */
class Lazy implements Interceptor
{
    private Closure $callback;

    /**
     * @param  callable(): Interceptor  $callback Interceptor factory callback.
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback instanceof Closure ? $callback : Closure::fromCallable($callback);
    }

    public function intercept(Context $context, Listener $listener): void
    {
        /** @var Interceptor $interceptor */
        $interceptor = ($this->callback)();
        $interceptor->intercept($context, $listener);
    }
}

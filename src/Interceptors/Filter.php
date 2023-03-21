<?php

declare(strict_types=1);

namespace BubbaOps\Framework\Interceptors;

use BubbaOps\Framework\Coerce;
use BubbaOps\Framework\Context;
use BubbaOps\Framework\Interceptor;
use BubbaOps\Framework\Listener;
use BubbaOps\Framework\Listeners\Undefined;

abstract class Filter implements Interceptor
{
    private Listener $defaultListener;

    /**
     * @param  Listener|callable|class-string|null  $defaultListener
     */
    public function __construct($defaultListener = null)
    {
        $this->defaultListener = $defaultListener ? Coerce::listener($defaultListener) : new Undefined();
    }

    public function intercept(Context $context, Listener $listener): void
    {
        $matched = $this->matches($context);
        $context->logger()->addContext(['filter:'.static::class => $matched ? 'match' : 'not-match']);

        if (! $matched) {
            $listener = $this->defaultListener;
        }

        $listener->handle($context);
    }

    abstract public function matches(Context $context): bool;
}

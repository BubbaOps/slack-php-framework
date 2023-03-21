<?php

declare(strict_types=1);

namespace BubbaOps\Framework\Listeners;

use BubbaOps\Framework\Context;
use BubbaOps\Framework\Listener;

/**
 * Wraps listener(s) for async/deferred execution.
 *
 * If a sync listener is not provided, then it defaults to an "ack". Either way, defer() is automatically used so that
 * the primary (async) listener will be established correctly for post-"ack" execution.
 */
class Async extends Base
{
    private Listener $asyncListener;

    private ?Listener $syncListener;

    public function __construct(Listener $asyncListener, ?Listener $syncListener = null)
    {
        $this->asyncListener = $asyncListener;
        $this->syncListener = $syncListener ?? new Ack();
    }

    protected function handleAck(Context $context): void
    {
        $this->syncListener->handle($context);
    }

    protected function handleAfterAck(Context $context): void
    {
        $this->asyncListener->handle($context);
    }
}

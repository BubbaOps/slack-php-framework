<?php

declare(strict_types=1);

namespace SlackPhp\Framework\Listeners;

use SlackPhp\Framework\{Context, Listener};

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

    /**
     * @param Listener $asyncListener
     * @param Listener|null $syncListener
     */
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

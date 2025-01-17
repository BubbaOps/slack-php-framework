<?php

declare(strict_types=1);

namespace BubbaOps\Framework\Listeners;

use BubbaOps\Framework\Context;
use BubbaOps\Framework\Exception;
use BubbaOps\Framework\Listener;
use Throwable;

class ClassResolver implements Listener
{
    private string $class;

    public function __construct(string $class)
    {
        $this->class = $class;
    }

    public function handle(Context $context): void
    {
        try {
            $listener = $context->container()->get($this->class);
        } catch (Throwable $ex) {
            throw new Exception('Could not resolve class name to Listener', 0, $ex);
        }

        if (! $listener instanceof Listener) {
            throw new Exception('Resolved class name to a non-Listener');
        }

        $context->logger()->addContext(['listener' => $this->class]);

        $listener->handle($context);
    }
}

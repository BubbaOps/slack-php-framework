<?php

declare(strict_types=1);

namespace BubbaOps\Framework\Listeners;

use BubbaOps\Framework\Context;
use BubbaOps\Framework\Listener;

class Undefined implements Listener
{
    public function handle(Context $context): void
    {
        $context->logger()->error('No listener matching payload');
    }
}

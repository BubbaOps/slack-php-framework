<?php

declare(strict_types=1);

namespace BubbaOps\Framework;

interface Interceptor
{
    public function intercept(Context $context, Listener $listener): void;
}

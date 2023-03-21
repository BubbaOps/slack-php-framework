<?php

declare(strict_types=1);

namespace BubbaOps\Framework;

interface Listener
{
    public function handle(Context $context): void;
}

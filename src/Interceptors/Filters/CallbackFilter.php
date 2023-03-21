<?php

declare(strict_types=1);

namespace BubbaOps\Framework\Interceptors\Filters;

use Closure;
use BubbaOps\Framework\Context;
use BubbaOps\Framework\Interceptors\Filter;
use BubbaOps\Framework\Listener;

class CallbackFilter extends Filter
{
    /** @var Closure */
    private $filterFn;

    /**
     * @param  Listener|callable|string|null  $defaultListener
     */
    public function __construct(callable $filterFn, $defaultListener = null)
    {
        $this->filterFn = $filterFn instanceof Closure ? $filterFn : Closure::fromCallable($filterFn);
        parent::__construct($defaultListener);
    }

    public function matches(Context $context): bool
    {
        return ($this->filterFn)($context);
    }
}

<?php

declare(strict_types=1);

namespace BubbaOps\Framework;

/**
 * A collection of easy-to-incorporate, interceptor-applying helper methods for wrapping listeners.
 */
class Route
{
    /**
     * @param  Listener|callable(Context): void|class-string  $asyncListener
     * @param  Listener|callable(Context): void|class-string|null  $syncListener
     */
    public static function async($asyncListener, $syncListener = null): Listener
    {
        if ($syncListener !== null) {
            $syncListener = Coerce::listener($syncListener);
        }

        return new Listeners\Async(Coerce::listener($asyncListener), $syncListener);
    }

    /**
     * @param  array<string, string>|callable  $filter
     * @param  Listener|callable(Context): void|class-string  $listener
     */
    public static function filter($filter, $listener): Listener
    {
        if (is_callable($filter)) {
            $interceptor = new Interceptors\Filters\CallbackFilter($filter);
        } elseif (is_array($filter)) {
            $interceptor = new Interceptors\Filters\FieldFilter($filter);
        } else {
            throw new Exception('Invalid listener filter');
        }

        return new Listeners\Intercepted($interceptor, Coerce::listener($listener));
    }

    /**
     * @param  Interceptor|callable(): Interceptor|array  $interceptor
     * @param  Listener|callable(Context): void|class-string  $listener
     */
    public static function intercept($interceptor, $listener): Listener
    {
        return new Listeners\Intercepted(Coerce::interceptor($interceptor), Coerce::listener($listener));
    }

    /**
     * @param  array<string, Listener|callable|class-string>  $listeners
     */
    public static function switch(string $field, array $listeners): Listener
    {
        return new Listeners\FieldSwitch($field, $listeners);
    }

    /**
     * @param  callable(Context): void  $callback
     * @param  Listener|callable(Context): void|class-string  $listener
     */
    public static function tap(callable $callback, $listener): Listener
    {
        return new Listeners\Intercepted(new Interceptors\Tap($callback), Coerce::listener($listener));
    }
}

<?php

declare(strict_types=1);

namespace BubbaOps\Framework;

use BubbaOps\BlockKit\Surfaces;

/**
 * Provides helpers for coercing loosely-typed values to their actual, desired type.
 */
final class Coerce
{
    /**
     * Coerces a Message-like value to an actual Message surface.
     *
     * @param  Surfaces\Message|array|string|callable(): Surfaces\Message  $message
     *
     * @internal
     */
    public static function message($message): Surfaces\Message
    {
        if (is_callable($message)) {
            $message = $message();
        }

        if ($message instanceof Surfaces\Message) {
            return $message;
        } elseif (is_string($message)) {
            return Surfaces\Message::new()->text($message);
        } elseif (is_array($message)) {
            return Surfaces\Message::fromArray($message);
        }

        throw new Exception('Invalid message content');
    }

    /**
     * Coerces a Modal-like value to an actual Modal surface.
     *
     * @param  Surfaces\Modal|array|string|callable(): Surfaces\Modal  $modal
     *
     * @internal
     */
    public static function modal($modal): Surfaces\Modal
    {
        if (is_callable($modal)) {
            $modal = $modal();
        }

        if ($modal instanceof Surfaces\Modal) {
            return $modal;
        } elseif (is_string($modal)) {
            return Surfaces\Modal::new()->title('Thanks')->text($modal);
        } elseif (is_array($modal)) {
            return Surfaces\Modal::fromArray($modal);
        }

        throw new Exception('Invalid modal content');
    }

    /**
     * Coerces an "App Home"-like value to an actual App Home surface.
     *
     * @param  Surfaces\AppHome|array|string|callable(): Surfaces\AppHome  $appHome
     *
     * @internal
     */
    public static function appHome($appHome): Surfaces\AppHome
    {
        if (is_callable($appHome)) {
            $appHome = $appHome();
        }

        if ($appHome instanceof Surfaces\AppHome) {
            return $appHome;
        } elseif (is_string($appHome)) {
            return Surfaces\AppHome::new()->text($appHome);
        } elseif (is_array($appHome)) {
            return Surfaces\AppHome::fromArray($appHome);
        }

        throw new Exception('Invalid app home content');
    }

    /**
     * Coerces a Listener-like value to an actual Listener.
     *
     * @param  Listener|callable(Context): void|class-string  $listener
     *
     * @internal
     */
    public static function listener($listener): Listener
    {
        if ($listener instanceof Listener) {
            return $listener;
        } elseif (is_string($listener)) {
            return new Listeners\ClassResolver($listener);
        } elseif (is_callable($listener)) {
            return new Listeners\Callback($listener);
        }

        throw new Exception('Invalid listener');
    }

    /**
     * Coerces an Interceptor-like value to an actual Interceptor.
     *
     * @param  Interceptor|callable(): Interceptor|array  $interceptor
     *
     * @internal
     */
    public static function interceptor($interceptor): Interceptor
    {
        if ($interceptor instanceof Interceptor) {
            return $interceptor;
        } elseif (is_array($interceptor)) {
            return new Interceptors\Chain($interceptor);
        } elseif (is_callable($interceptor)) {
            return new Interceptors\Lazy($interceptor);
        }

        throw new Exception('Invalid interceptor');
    }

    /**
     * Coerces an Application-like value to an actual Application.
     *
     * @param  Application|Listener|callable(Context): void|class-string  $application
     */
    public static function application($application): Application
    {
        if ($application instanceof Application) {
            return $application;
        }

        return new Application(self::listener($application));
    }
}

<?php

declare(strict_types=1);

namespace SlackPhp\Framework\Interceptors;

use SlackPhp\Framework\Context;
use SlackPhp\Framework\Contexts\PayloadType;
use SlackPhp\Framework\Interceptor;
use SlackPhp\Framework\Listener;

class UrlVerification implements Interceptor
{
    public function intercept(Context $context, Listener $listener): void
    {
        $payload = $context->payload();
        if ($payload->isType(PayloadType::urlVerification())) {
            $challenge = (string) $payload->get('challenge', true);
            $context->ack(compact('challenge'));
        } else {
            $listener->handle($context);
        }
    }
}

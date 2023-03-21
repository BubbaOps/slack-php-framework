<?php

declare(strict_types=1);

namespace BubbaOps\Framework\Interceptors;

use BubbaOps\Framework\Context;
use BubbaOps\Framework\Contexts\PayloadType;
use BubbaOps\Framework\Interceptor;
use BubbaOps\Framework\Listener;

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

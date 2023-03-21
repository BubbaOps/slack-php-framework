<?php

declare(strict_types=1);

namespace BubbaOps\Framework\Http;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface as HandlerInterface;
use BubbaOps\Framework\Application;
use BubbaOps\Framework\Context;
use BubbaOps\Framework\Deferral\PreAckDeferrer;
use BubbaOps\Framework\Deferrer;

class AppHandler implements HandlerInterface
{
    private ?Deferrer $deferrer;

    private Application $app;

    public function __construct(Application $app, ?Deferrer $deferrer = null)
    {
        $this->app = $app;
        $this->deferrer = $deferrer ?? new PreAckDeferrer($app);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Prepare the app context for the listener(s).
        $context = Util::createContextFromRequest($request);

        // Delegate to the listener(s) for handling the app context.
        $this->app->handle($context);
        if ($context->isDeferred()) {
            $this->deferrer->defer($context);
        }

        return $this->createResponseFromContext($context);
    }

    public function createResponseFromContext(Context $context): ResponseInterface
    {
        if (! $context->isAcknowledged()) {
            throw new HttpException('No ack provided by the app');
        }

        $ack = $context->getAck();
        if ($ack === null) {
            return new Response(200);
        }

        return new Response(200, [
            'Content-Type' => 'application/json',
            'Content-Length' => strlen($ack),
        ], $ack);
    }
}

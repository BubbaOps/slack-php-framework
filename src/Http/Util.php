<?php

declare(strict_types=1);

namespace BubbaOps\Framework\Http;

use function array_filter;
use const ARRAY_FILTER_USE_KEY;
use JsonException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as HandlerInterface;
use BubbaOps\Framework\Context;
use BubbaOps\Framework\Contexts\Payload;
use function strpos;
use Throwable;

abstract class Util
{
    /**
     * Wraps handler with middleware, but keeps the handler interface.
     *
     * @param  MiddlewareInterface[]  $middlewares
     */
    public static function applyMiddleware(HandlerInterface $handler, array $middlewares): HandlerInterface
    {
        foreach ($middlewares as $middleware) {
            $handler = new class($handler, $middleware) implements HandlerInterface
            {
                /** @var HandlerInterface */
                private $handler;

                /** @var MiddlewareInterface */
                private $middleware;

                public function __construct(HandlerInterface $handler, MiddlewareInterface $middleware)
                {
                    $this->handler = $handler;
                    $this->middleware = $middleware;
                }

                public function handle(ServerRequestInterface $request): ResponseInterface
                {
                    return $this->middleware->process($request, $this->handler);
                }
            };
        }

        return $handler;
    }

    /**
     * @throws HttpException if context cannot be created.
     */
    public static function createContextFromRequest(ServerRequestInterface $request): Context
    {
        $payload = Util::parseRequestBody($request);

        // Create the context with data from the HTTP request.
        return new Context($payload, $request->getAttributes() + [
            'timestamp' => (int) $request->getHeaderLine('X-Slack-Request-Timestamp'),
            'http' => [
                'query' => $request->getQueryParams(),
                'headers' => array_filter(
                    $request->getHeaders(),
                    fn (string $key) => strpos($key, 'X-Slack') === 0,
                    ARRAY_FILTER_USE_KEY
                ),
            ],
        ]);
    }

    /**
     * @throws HttpException if payload cannot be parsed.
     */
    public static function parseRequestBody(ServerRequestInterface $request): Payload
    {
        try {
            $body = self::readRequestBody($request);
            $contentType = $request->getHeaderLine('Content-Type');

            return Payload::fromHttpRequest($body, $contentType);
        } catch (JsonException $ex) {
            throw new HttpException('Could not parse json in request body', 400, $ex);
        } catch (Throwable $ex) {
            throw new HttpException('Could not parse payload from request body', 400, $ex);
        }
    }

    public static function readRequestBody(ServerRequestInterface $request): string
    {
        if ($request->getMethod() !== 'POST') {
            throw new HttpException("Request method \"{$request->getMethod()}\" not allowed", 405);
        }

        $body = $request->getBody();
        $bodyContent = (string) $body;
        $body->rewind();

        if (strlen($bodyContent) === 0) {
            throw new HttpException('Request body is empty', 400);
        }

        return $bodyContent;
    }
}

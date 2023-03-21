<?php

namespace BubbaOps\Framework\Tests\Integration;

use PHPUnit\Framework\TestCase;
use BubbaOps\BlockKit\Surfaces\Message;
use BubbaOps\Framework\App;
use BubbaOps\Framework\Clients\RespondClient;
use BubbaOps\Framework\Context;
use BubbaOps\Framework\Deferral\DeferredContextCliServer;

class DeferredContextCliServerTest extends TestCase
{
    public function testCanProcessDeferredContext(): void
    {
        $serializedContext = base64_encode(json_encode([
            '_acknowledged' => true,
            '_deferred' => true,
            '_payload' => [
                'command' => '/foo',
                'response_url' => 'https://example.org',
            ],
        ]));

        $respondClient = $this->createMock(RespondClient::class);
        $respondClient->expects($this->once())
            ->method('respond')
            ->with(
                'https://example.org',
                $this->callback(fn ($v): bool => $v instanceof Message && strpos($v->toJson(), 'bar') !== false)
            );

        $app = App::new()
            ->commandAsync('foo', fn (Context $ctx) => $ctx->respond('bar'))
            ->tap(function (Context $ctx) use ($respondClient) {
                $ctx->withRespondClient($respondClient);
            });
        DeferredContextCliServer::new()
            ->withApp($app)
            ->withArgs(['script', $serializedContext, '--soft-exit'])
            ->start();
    }
}

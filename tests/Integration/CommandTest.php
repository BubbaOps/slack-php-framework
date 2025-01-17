<?php

namespace BubbaOps\Framework\Tests\Integration;

use BubbaOps\Framework\App;
use BubbaOps\Framework\Commands\CommandListener;
use BubbaOps\Framework\Commands\DefinitionBuilder;
use BubbaOps\Framework\Commands\Input;
use BubbaOps\Framework\Context;

class CommandTest extends IntegTestCase
{
    public function testCanHandleCommandRequest(): void
    {
        $this->failOnLoggedErrors();

        $request = $this->createCommandRequest([
            'command' => '/test',
            'text' => 'hello',
        ]);

        $listener = function (Context $ctx) {
            $payload = $ctx->payload();
            $ctx->ack("{$payload->get('command')} {$payload->get('text')}");
        };

        App::new()
            ->command('test', $listener)
            ->run($this->createHttpServer($request));

        $result = $this->parseResponse();
        $this->assertEquals('/test hello', $result->get('blocks.0.text.text'));
    }

    public function testCanHandleSubCommandRequest(): void
    {
        $this->failOnLoggedErrors();
        $request = $this->createCommandRequest([
            'command' => '/test',
            'text' => 'hello Jeremy --caps',
        ]);

        $listener = new class() extends CommandListener
        {
            protected static function buildDefinition(DefinitionBuilder $builder): DefinitionBuilder
            {
                return $builder->name('test')->subCommand('hello')->arg('name')->opt('caps');
            }

            protected function listenToCommand(Context $context, Input $input): void
            {
                $text = "Hello, {$input->get('name')}";
                $context->ack($input->get('caps') ? strtoupper($text) : $text);
            }
        };

        App::new()
            ->commandGroup('test', ['hello' => $listener])
            ->run($this->createHttpServer($request));

        $result = $this->parseResponse();
        $this->assertEquals('HELLO, JEREMY', $result->get('blocks.0.text.text'));
    }
}

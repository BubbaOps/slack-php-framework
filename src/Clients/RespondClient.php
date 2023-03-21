<?php

declare(strict_types=1);

namespace BubbaOps\Framework\Clients;

use BubbaOps\BlockKit\Surfaces\Message;
use BubbaOps\Framework\Exception;

interface RespondClient
{
    /**
     * Responds to a Slack message using a response_url.
     *
     * @param  string  $responseUrl URL used to respond to Slack message
     * @param  Message  $message Message to respond with
     *
     * @throws Exception if responding was not successful
     */
    public function respond(string $responseUrl, Message $message): void;
}

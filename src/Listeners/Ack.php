<?php

declare(strict_types=1);

namespace BubbaOps\Framework\Listeners;

use BubbaOps\BlockKit\Surfaces\Message;
use BubbaOps\Framework\Coerce;
use BubbaOps\Framework\Context;
use BubbaOps\Framework\Listener;

/**
 * Simple listener that merely acks.
 *
 * Allows for a provided message to be included in the ack, which is especially useful for commands.
 */
class Ack implements Listener
{
    private ?Message $message;

    /**
     * @param  Message|array|string|null  $message Message to include in ack (for commands).
     */
    public function __construct($message = null)
    {
        $this->message = $message ? Coerce::message($message) : null;
    }

    public function handle(Context $context): void
    {
        $context->ack($this->message);
    }
}

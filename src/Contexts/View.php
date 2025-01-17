<?php

declare(strict_types=1);

namespace BubbaOps\Framework\Contexts;

use BubbaOps\BlockKit\Surfaces\Modal;
use BubbaOps\Framework\Coerce;
use BubbaOps\Framework\Context;

class View
{
    private Context $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function clear(): void
    {
        $this->context->ack([
            'response_action' => 'clear',
        ]);
    }

    public function close(): void
    {
        $this->context->ack();
    }

    public function errors(array $errors): void
    {
        $this->context->ack([
            'response_action' => 'errors',
            'errors' => $errors,
        ]);
    }

    /**
     * @param  Modal|array|string|callable(): Modal  $modal
     */
    public function push($modal): void
    {
        $this->context->ack([
            'response_action' => 'push',
            'view' => Coerce::modal($modal),
        ]);
    }

    /**
     * @param  Modal|array|string|callable(): Modal  $modal
     */
    public function update($modal): void
    {
        $this->context->ack([
            'response_action' => 'update',
            'view' => Coerce::modal($modal),
        ]);
    }
}

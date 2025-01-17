<?php

declare(strict_types=1);

namespace BubbaOps\Framework\Listeners;

use Closure;
use BubbaOps\Framework\Coerce;
use BubbaOps\Framework\Context;
use BubbaOps\Framework\Listener;

class FieldSwitch implements Listener
{
    /** @var array<string, Listener> */
    private array $cases;

    private ?Listener $default;

    private string $field;

    public function __construct(string $field, array $cases, $default = null)
    {
        $default ??= $cases['*'] ?? null;
        if ($default !== null) {
            $this->default = Coerce::listener($default);
            unset($cases['*']);
        }

        $this->field = $field;
        $this->cases = array_map(Closure::fromCallable([Coerce::class, 'listener']), $cases);
    }

    public function handle(Context $context): void
    {
        $value = $context->payload()->get($this->field);
        $listener = $this->cases[$value] ?? $this->default ?? new Undefined();
        $listener->handle($context);
    }
}

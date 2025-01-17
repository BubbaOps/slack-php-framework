<?php

declare(strict_types=1);

namespace BubbaOps\Framework\Commands;

use BubbaOps\Framework\Contexts\HasData;

class Input
{
    use HasData;

    private Definition $definition;

    public function __construct(string $commandText, Definition $definition)
    {
        $this->definition = $definition;
        $parser = new Parser($this->definition);
        $this->setData($parser->parse($commandText));
    }

    public function getDefinition(): Definition
    {
        return $this->definition;
    }
}

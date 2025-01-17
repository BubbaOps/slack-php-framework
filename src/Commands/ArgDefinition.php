<?php

declare(strict_types=1);

namespace BubbaOps\Framework\Commands;

class ArgDefinition
{
    public const TYPE_STRING = 'string';

    public const TYPE_INT = 'int';

    public const TYPE_FLOAT = 'float';

    public const TYPE_BOOL = 'bool';

    public const REQUIRED = true;

    public const OPTIONAL = false;

    private string $name;

    private bool $required;

    private string $description;

    private string $type;

    public function __construct(
        string $name,
        string $type = self::TYPE_STRING,
        bool $required = true,
        string $description = ''
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->required = $required;
        $this->description = $description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getFormat(): string
    {
        $format = "<{$this->name}:{$this->type}>";
        if (! $this->required) {
            $format = "[{$format}]";
        }

        return $format;
    }
}

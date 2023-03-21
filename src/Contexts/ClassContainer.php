<?php

declare(strict_types=1);

namespace BubbaOps\Framework\Contexts;

use function class_exists;
use Psr\Container\ContainerInterface;
use BubbaOps\Framework\Exception;

class ClassContainer implements ContainerInterface
{
    public function get($id)
    {
        if (! $this->has($id)) {
            throw new Exception("Class does not exist: {$id}");
        }

        return new $id();
    }

    public function has($id): bool
    {
        return class_exists($id);
    }
}

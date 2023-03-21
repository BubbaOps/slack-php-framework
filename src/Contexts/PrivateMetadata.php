<?php

declare(strict_types=1);

namespace BubbaOps\Framework\Contexts;

use ArrayAccess;

class PrivateMetadata implements ArrayAccess
{
    use HasData;

    public static function decode(string $base64Encoded): self
    {
        $urlEncoded = base64_decode($base64Encoded);
        parse_str($urlEncoded, $data);

        return new self($data);
    }

    public static function encode(array $data): string
    {
        return base64_encode(http_build_query($data));
    }

    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset] ?? null;
    }

    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    public function __toString(): string
    {
        return self::encode($this->data);
    }
}

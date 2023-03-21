<?php

declare(strict_types=1);

namespace BubbaOps\Framework\Contexts;

use JsonSerializable;

class DataBag implements JsonSerializable
{
    use HasData;
}

<?php

declare(strict_types=1);

namespace BubbaOps\Framework\Contexts;

use BubbaOps\BlockKit\Config;
use BubbaOps\BlockKit\Kit;
use BubbaOps\BlockKit\Partials\OptionList;
use BubbaOps\BlockKit\Surfaces;

class Blocks
{
    public static function new(): self
    {
        return new self();
    }

    public function appHome(): Surfaces\AppHome
    {
        return Kit::newAppHome();
    }

    public function message(): Surfaces\Message
    {
        return Kit::newMessage();
    }

    public function modal(): Surfaces\Modal
    {
        return Kit::newModal();
    }

    public function optionList(): OptionList
    {
        return OptionList::new();
    }

    public function config(): Config
    {
        return Kit::config();
    }
}

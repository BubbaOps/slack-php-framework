<?php

declare(strict_types=1);

namespace SlackPhp\Framework\Contexts;

use SlackPhp\BlockKit\Config;
use SlackPhp\BlockKit\Kit;
use SlackPhp\BlockKit\Partials\OptionList;
use SlackPhp\BlockKit\Surfaces;

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

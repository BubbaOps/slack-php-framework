<?php

use SlackPhp\Framework\App;
use SlackPhp\Framework\Context;

return App::new()->any(fn (Context $ctx) => $ctx->ack('hello'));

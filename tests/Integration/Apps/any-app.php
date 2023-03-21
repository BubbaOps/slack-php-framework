<?php

use BubbaOps\Framework\App;
use BubbaOps\Framework\Context;

return App::new()->any(fn (Context $ctx) => $ctx->ack('hello'));

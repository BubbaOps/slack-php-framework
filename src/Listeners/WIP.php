<?php

declare(strict_types=1);

namespace BubbaOps\Framework\Listeners;

use BubbaOps\Framework\Context;
use BubbaOps\Framework\Contexts\PayloadType;
use BubbaOps\Framework\Listener;

/**
 * Simple listener that displays/logs a "Work in progress" message in whichever medium makes the most sense.
 */
class WIP implements Listener
{
    public function handle(Context $context): void
    {
        $hasApi = $context->getAppConfig()->getAppCredentials()->supportsApiAuth();
        $data = $context->payload();

        $message = 'Work in progress';
        if ($data->isType(PayloadType::viewSubmission())) {
            $context->view()->push($message);
        } elseif ($data->getResponseUrl()) {
            $context->respond($message);
        } elseif ($hasApi && $data->isType(PayloadType::eventCallback()) && $data->getTypeId() === 'app_home_opened') {
            $context->appHome()->update($message);
        } elseif ($hasApi && $data->get('trigger_id')) {
            // If a modal is already open, push a new one on the stack, otherwise, open a new stack.
            if ($data->get('view.type') === 'modal') {
                $context->modals()->push($message);
            } else {
                $context->modals()->open($message);
            }
        } else {
            $context->logger()->debug($message);
        }

        if (! $context->isAcknowledged()) {
            $context->ack();
        }
    }
}

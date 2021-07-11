<?php

declare(strict_types=1);

namespace SlackPhp\Framework\Listeners;

use SlackPhp\Framework\{Context, Listener};
use SlackPhp\Framework\Contexts\PayloadType;

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
            $context->home($message);
        } elseif ($hasApi && $data->get('trigger_id')) {
            $context->modals()->open($message);
        } else {
            $context->logger()->debug($message);
        }

        if (!$context->isAcknowledged()) {
            $context->ack();
        }
    }
}

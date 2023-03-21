<?php

declare(strict_types=1);

namespace BubbaOps\Framework\Contexts;

use BubbaOps\Framework\Context;
use BubbaOps\Framework\Exception;
use Throwable;

/**
 * Provides a simple way to log errors and provide an error message to the user.
 */
class Error
{
    private Context $context;

    private Throwable $exception;

    private array $additionalContext;

    private string $explanation;

    public function __construct(Context $context, Throwable $exception)
    {
        $this->context = $context;
        $this->exception = $exception;
        $this->explanation = 'An error occurred in the application.';
        $this->additionalContext = [];
    }

    /**
     * Adds a human-readable explanation of the error that's safe to provide to the application user.
     *
     * @return $this
     */
    public function addExplanation(string $explanation): self
    {
        $this->explanation = $explanation;

        return $this;
    }

    /**
     * Adds additional context to the error that will be included in the logs.
     *
     * @return $this
     */
    public function addAdditionalContext(array $additionalContext): self
    {
        $this->additionalContext += $additionalContext;

        return $this;
    }

    /**
     * Logs error and displays error on the App Home.
     */
    public function appHome(): void
    {
        $this->log();
        $appHomeFactory = $this->context->getAppConfig()->getErrorAppHomeFactory();
        $this->context->appHome()->update($appHomeFactory($this->explanation));
    }

    /**
     * Logs error and displays error in a modal.
     */
    public function modal(): void
    {
        $this->log();
        $modalFactory = $this->context->getAppConfig()->getErrorModalFactory();
        $modal = $modalFactory($this->explanation);
        if ($this->context->payload()->get('view.type') === 'modal') {
            $this->context->modals()->push($modal);
        } else {
            $this->context->modals()->open($modal);
        }
    }

    /**
     * Logs error and sends error to the user as a message.
     */
    public function message(): void
    {
        $this->log();
        $messageFactory = $this->context->getAppConfig()->getErrorMessageFactory();
        $message = $messageFactory($this->explanation)->ephemeral();
        $responseUrl = $this->context->payload()->getResponseUrl();
        if ($responseUrl !== null) {
            $this->context->respond($message, $responseUrl);
        } else {
            $this->context->say($message, $this->context->payload()->getUserId());
        }
    }

    /**
     * Rethrows the error as a new exception.
     *
     * @throws Exception
     */
    public function reThrow(): void
    {
        $exception = $this->exception instanceof Exception
            ? $this->exception
            : new Exception($this->explanation, 0, $this->exception);

        $exception->addContext($this->additionalContext);

        throw $exception;
    }

    /**
     * Logs error rethrows it as a new exception.
     *
     * @throws Exception
     */
    public function logAndReThrow(): void
    {
        $this->log();
        $this->reThrow();
    }

    private function log(): void
    {
        $context = $this->additionalContext + ['exception' => $this->exception];
        $this->context->logger()->error($this->explanation, $context);
    }
}

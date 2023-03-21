<?php

declare(strict_types=1);

namespace BubbaOps\Framework;

use Closure;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use BubbaOps\BlockKit\Surfaces\AppHome;
use BubbaOps\BlockKit\Surfaces\Message;
use BubbaOps\BlockKit\Surfaces\Modal;
use BubbaOps\Framework\Auth\AppCredentials;
use BubbaOps\Framework\Auth\SingleTeamTokenStore;
use BubbaOps\Framework\Auth\TokenStore;
use BubbaOps\Framework\Contexts\ClassContainer;

/**
 * Configuration for a Slack Application.
 */
class AppConfig
{
    private ?string $alias;

    private ?AppCredentials $appCredentials;

    private ?string $appToken;

    private ?string $botToken;

    private ?string $clientId;

    private ?string $clientSecret;

    private ?ContainerInterface $container;

    private ?Env $env;

    private Closure $errorAppHomeFactory;

    private Closure $errorMessageFactory;

    private Closure $errorModalFactory;

    private ?string $id;

    private ?SlackLogger $logger;

    /** @var string[]|null */
    private ?array $scopes;

    private ?string $signingKey;

    private ?string $stateSecret;

    private ?TokenStore $tokenStore;

    /**
     * Sets the prefix to use for fetching environment variables.
     *
     * The default is "SLACK", but setting an app-specific one may be necessary for multi-tenant apps.
     *
     * @return $this
     */
    public function withEnvPrefix(string $prefix): self
    {
        $this->env = Env::vars($prefix);

        return $this;
    }

    public function getEnv(): Env
    {
        return $this->env ??= Env::vars();
    }

    /**
     * Sets the App ID.
     *
     * Typically not required, as it will either be set for you or not required. You can set this explicitly to a) make
     * sure it gets included in log messages, and b) make sure the app validates that incoming requests match IDs.
     *
     * @return $this
     */
    public function withId(string $id): self
    {
        $this->id = $id;
        $this->getLogger()->addContext(['app_id' => $id]);

        return $this;
    }

    public function getId(): ?string
    {
        return $this->id ??= $this->getEnv()->getAppId();
    }

    /**
     * Sets a human-readable app alias to be used in log messages.
     *
     * @return $this
     */
    public function withAlias(string $alias): self
    {
        $this->alias = $alias;
        $this->getLogger()
            ->withName($alias)
            ->addContext(['app_name' => $alias]);

        return $this;
    }

    public function getAlias(): ?string
    {
        return $this->alias ?? null;
    }

    /**
     * Sets the PSR-3 logger instance to use with the app.
     *
     * The PSR-3 logger gets wrapped by a custom logger implementation that adds additional context to messages.
     *
     * @return $this
     */
    public function withLogger(LoggerInterface $logger): self
    {
        $this->logger = SlackLogger::wrap($logger)
            ->withName($this->getAlias())
            ->addContext(array_filter([
                'app_id' => $this->getId(),
                'app_name' => $this->getAlias(),
            ]));

        return $this;
    }

    public function getLogger(): SlackLogger
    {
        if (! isset($this->logger)) {
            $this->withLogger(new NullLogger());
        }

        return $this->logger;
    }

    public function hasLogger(): bool
    {
        return isset($this->logger);
    }

    /**
     * Sets the PSR-11 container to use to resolve Listener class names to Listener class instances.
     *
     * This is only needed if you are using class names to configure Listeners AND those Listeners require dependencies
     * to be configured/resolved. The container is also accessible to the Context, if you need to use it as a "service
     * locator" as well (possible, but discouraged).
     *
     * @return $this
     */
    public function withContainer(ContainerInterface $container): self
    {
        $this->container = $container;

        return $this;
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container ??= new ClassContainer();
    }

    /**
     * Sets a TokenStore for the app, which is used as a service to get a "bot token" for a given team/enterprise ID.
     *
     * A TokenStore is needed when an app is distributed to more than one team (aka workspace) or enterprise org. In
     * other words, when more than one API token can be used, the Token Store is what provides it.
     *
     * @return $this
     */
    public function withTokenStore(TokenStore $tokenStore): self
    {
        $this->tokenStore = $tokenStore;

        return $this;
    }

    public function getTokenStore(): TokenStore
    {
        if (! isset($this->tokenStore)) {
            $appCredentials = $this->getAppCredentials();
            $botToken = $appCredentials->supportsApiAuth()
                ? $appCredentials->getDefaultBotToken()
                : $this->getBotToken();

            $this->tokenStore = new SingleTeamTokenStore($botToken);
        }

        return $this->tokenStore;
    }

    /**
     * Explicitly sets the both token to use for Auth.
     *
     * You can also set this via the environment variable: SLACK_BOT_TOKEN.
     *
     * @return $this
     */
    public function withBotToken(string $botToken): self
    {
        $this->botToken = $botToken;

        return $this;
    }

    public function getBotToken(): ?string
    {
        return $this->botToken ??= $this->getEnv()->getBotToken();
    }

    /**
     * Explicitly sets the signing key to use for Auth.
     *
     * You can also set this via the environment variable: SLACK_SIGNING_KEY.
     *
     * @return $this
     */
    public function withSigningKey(string $signingKey): self
    {
        $this->signingKey = $signingKey;

        return $this;
    }

    public function getSigningKey(): ?string
    {
        return $this->signingKey ??= $this->getEnv()->getSigningKey();
    }

    /**
     * Explicitly sets the client ID to use for OAuth.
     *
     * You can also set this via the environment variable: SLACK_CLIENT_ID.
     *
     * @return $this
     */
    public function withClientId(string $clientId): self
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function getClientId(): ?string
    {
        return $this->clientId ??= $this->getEnv()->getClientId();
    }

    /**
     * Explicitly sets the client secret to use for OAuth.
     *
     * You can also set this via the environment variable: SLACK_CLIENT_SECRET.
     *
     * @return $this
     */
    public function withClientSecret(string $clientSecret): self
    {
        $this->clientSecret = $clientSecret;

        return $this;
    }

    public function getClientSecret(): ?string
    {
        return $this->clientSecret ?? $this->getEnv()->getClientSecret();
    }

    /**
     * Explicitly sets the state secret to use for OAuth.
     *
     * The "state secret" is a fixed value for your app that is sent to Slack during the OAuth flow and then verified
     * by the app when Slack redirects the user back to the app. You can also set this via the environment
     * variable: SLACK_STATE_SECRET.
     *
     * @return $this
     */
    public function withStateSecret(string $stateSecret): self
    {
        $this->stateSecret = $stateSecret;

        return $this;
    }

    public function getStateSecret(): ?string
    {
        return $this->stateSecret ?? $this->getEnv()->getStateSecret();
    }

    /**
     * Sets the required scopes needed for the app. These are needed for the OAuth flow to set up app permissions.
     *
     * @param  string[]  $scopes
     * @return $this
     */
    public function withScopes(array $scopes): self
    {
        $this->scopes = $scopes;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getScopes(): array
    {
        return $this->scopes ?? $this->getEnv()->getScopes();
    }

    /**
     * Sets the app credentials for the app.
     *
     * These credentials are an encapsulation of all the various app-specific keys and secrets needed for auth.
     *
     * @return $this
     */
    public function withAppCredentials(AppCredentials $appCredentials): self
    {
        $this->appCredentials = $appCredentials;

        return $this;
    }

    public function getAppCredentials(): AppCredentials
    {
        return $this->appCredentials ??= new AppCredentials(
            $this->getSigningKey(),
            $this->getBotToken(),
            $this->getClientId(),
            $this->getClientSecret(),
            $this->getStateSecret(),
            $this->getAppToken(),
        );
    }

    /**
     * Explicitly sets the app token to use for Socket Mode auth.
     *
     * @return $this
     */
    public function withAppToken(string $appToken): self
    {
        $this->appToken = $appToken;

        return $this;
    }

    public function getAppToken(): ?string
    {
        return $this->appToken ?? $this->getEnv()->getAppToken();
    }

    /**
     * Sets the callback for generating the error message when using `$context->error($ex)->message()`.
     *
     * @param  callable(string): Message  $factoryFn
     * @return $this
     */
    public function withErrorMessageFactory(callable $factoryFn): self
    {
        $this->errorMessageFactory = Closure::fromCallable($factoryFn);

        return $this;
    }

    /**
     * Retrieves the callback for generating the error message when using `$context->error($ex)->message()`.
     */
    public function getErrorMessageFactory(): Closure
    {
        return $this->errorMessageFactory
            ?? function (string $message): Message {
                return Message::new()
                    ->text(':warning: An Error Occurred')
                    ->text("> {$message}");
            };
    }

    /**
     * Sets the callback for generating the error modal when using `$context->error($ex)->modal()`.
     *
     * @param  callable(string): Modal  $factoryFn
     * @return $this
     */
    public function withErrorModalFactory(callable $factoryFn): self
    {
        $this->errorModalFactory = Closure::fromCallable($factoryFn);

        return $this;
    }

    /**
     * Retrieves the callback for generating the error modal when using `$context->error($ex)->modal()`.
     */
    public function getErrorModalFactory(): Closure
    {
        return $this->errorModalFactory
            ?? function (string $message): Modal {
                return Modal::new()
                    ->title('Error')
                    ->text(':warning: An Error Occurred')
                    ->text("> {$message}");
            };
    }

    /**
     * Sets the callback for generating the error modal when using `$context->error($ex)->appHome()`.
     *
     * @param  callable(string): AppHome  $factoryFn
     * @return $this
     */
    public function withErrorAppHomeFactory(callable $factoryFn): self
    {
        $this->errorAppHomeFactory = Closure::fromCallable($factoryFn);

        return $this;
    }

    /**
     * Retrieves the callback for generating the error modal when using `$context->error($ex)->appHome()`.
     */
    public function getErrorAppHomeFactory(): Closure
    {
        return $this->errorAppHomeFactory
            ?? function (string $message): AppHome {
                return AppHome::new()
                    ->text(':warning: An Error Occurred')
                    ->text("> {$message}");
            };
    }
}

<?php

declare(strict_types=1);

namespace BubbaOps\Framework\Auth;

use BubbaOps\Framework\Env;
use BubbaOps\Framework\Exception;

class SingleAppCredentialsStore implements AppCredentialsStore
{
    private AppCredentials $appCredentials;

    public function __construct(
        ?string $signingKey = null,
        ?string $defaultBotToken = null,
        ?string $clientId = null,
        ?string $clientSecret = null
    ) {
        $env = Env::vars();
        $signingKey ??= $env->getSigningKey();
        if ($signingKey === null) {
            throw new Exception('Signing key not set for App');
        }

        $this->appCredentials = new AppCredentials(
            $signingKey,
            $defaultBotToken ?? $env->getBotToken(),
            $clientId ?? $env->getClientId(),
            $clientSecret ?? $env->getClientSecret(),
        );
    }

    /**
     * @throws Exception if bot app credentials cannot be retrieved
     */
    public function getAppCredentials(string $appId): AppCredentials
    {
        return $this->appCredentials;
    }
}

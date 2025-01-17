<?php

declare(strict_types=1);

namespace BubbaOps\Framework\Auth;

interface AppCredentialsStore
{
    /**
     * @throws AuthException if bot app credentials cannot be retrieved
     */
    public function getAppCredentials(string $appId): AppCredentials;
}

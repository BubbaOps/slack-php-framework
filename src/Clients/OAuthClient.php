<?php

declare(strict_types=1);

namespace BubbaOps\Framework\Clients;

use function array_filter;
use BubbaOps\Framework\Exception;

class OAuthClient
{
    private ApiClient $apiClient;

    public function __construct(?ApiClient $apiClient = null)
    {
        $this->apiClient = $apiClient ?? new SimpleApiClient(null);
    }

    /**
     * @return array Includes access_token, team.id, and enterprise.id fields
     *
     * @throws Exception
     */
    public function createAccessToken(
        string $clientId,
        string $clientSecret,
        string $temporaryAccessCode,
        ?string $redirectUri = null
    ): array {
        return $this->apiClient->call('oauth.v2.access', array_filter([
            'code' => $temporaryAccessCode,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
        ]));
    }

    /**
     * @throws Exception
     */
    public function revokeAccessToken(string $accessToken, ?bool $test = null): array
    {
        return $this->apiClient->call('auth.revoke', [
            'token' => $accessToken,
            'test' => (int) $test,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace BubbaOps\Framework\Auth;

use BubbaOps\Framework\Exception;

/**
 * @TODO I have not considered the user token case yet. This may need changes later.
 */
interface TokenStore
{
    /**
     * @return string
     *
     * @throws Exception if bot token is not available
     */
    public function get(?string $teamId, ?string $enterpriseId): ?string;

    public function set(?string $teamId, ?string $enterpriseId, string $token): void;
}

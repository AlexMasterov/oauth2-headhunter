<?php
declare(strict_types=1);

namespace AlexMasterov\OAuth2\Client\Provider\Tests;

use League\OAuth2\Client\Token\AccessToken;

trait CanAccessTokenStub
{
    protected function accessToken(...$args): AccessToken
    {
        $default = [
            'access_token' => bin2hex(random_bytes(128)),
            'expires_in'   => 3600,
        ];

        $values = array_replace($default, ...$args);

        return new AccessToken($values);
    }
}

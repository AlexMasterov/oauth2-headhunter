<?php
declare(strict_types=1);

namespace AlexMasterov\OAuth2\Client\Provider\Tests;

use AlexMasterov\OAuth2\Client\Provider\{
    HeadHunter,
    HeadHunterException,
    HeadHunterResourceOwner,
    Tests\CanAccessTokenStub,
    Tests\CanMockHttp
};
use PHPUnit\Framework\TestCase;

class HeadHunterTest extends TestCase
{
    use CanAccessTokenStub;
    use CanMockHttp;

    public function testAuthorizationUrl()
    {
        // Execute
        $url = $this->provider()
            ->getAuthorizationUrl();

        // Verify
        self::assertSame('/oauth/authorize', path($url));
    }

    public function testBaseAccessTokenUrl()
    {
        static $params = [];

        // Execute
        $url = $this->provider()
            ->getBaseAccessTokenUrl($params);

        // Verify
        self::assertSame('/oauth/token', path($url));
    }

    public function testResourceOwnerDetailsUrl()
    {
        // Stub
        $apiUrl = $this->apiUrl();
        $tokenParams = [
            'access_token'  => 'mock_access_token',
        ];

        $accessToken = $tokenParams['access_token'];

        // Execute
        $detailUrl = $this->provider()
            ->getResourceOwnerDetailsUrl($this->accessToken($tokenParams));

        // Verify
        self::assertSame(
            "{$apiUrl}/me?access_token={$accessToken}",
            $detailUrl
        );
    }

    public function testDefaultScopes()
    {
        $getDefaultScopes = function () {
            return $this->getDefaultScopes();
        };

        // Execute
        $defaultScopes = $getDefaultScopes->call($this->provider());

        // Verify
        self::assertSame([], $defaultScopes);
    }

    public function testCheckResponse()
    {
        $getParseResponse = function () use (&$response, &$data) {
            return $this->checkResponse($response, $data);
        };

        // Stub
        $code = 400;
        $data = [
            'error'             => 'Foo error',
            'error_description' => 'Error description',
        ];

        // Mock
        $response = $this->mockResponse('', '', $code);

        // Verify
        self::expectException(HeadHunterException::class);
        self::expectExceptionCode($code);
        self::expectExceptionMessage(implode(': ', $data));

        // Execute
        $getParseResponse->call($this->provider());
    }

    public function testCreateResourceOwner()
    {
        $getCreateResourceOwner = function () use (&$response, &$token) {
            return $this->createResourceOwner($response, $token);
        };

        // Stub
        $token = $this->accessToken();
        $response = [
            'id'           => random_int(1, 1000),
            'last_name'   => 'mock_last_name',
            'first_name'  => 'mock_first_name',
            'middle_name' => 'mock_middle_name',
            'email'       => 'mock_email',
        ];

        // Execute
        $resourceOwner = $getCreateResourceOwner->call($this->provider());

        // Verify
        self::assertInstanceOf(HeadHunterResourceOwner::class, $resourceOwner);
        self::assertEquals($response['id'], $resourceOwner->getId());
        self::assertEquals($response['last_name'], $resourceOwner->getLastName());
        self::assertEquals($response['first_name'], $resourceOwner->getFirstName());
        self::assertEquals($response['middle_name'], $resourceOwner->getMiddleName());
        self::assertSame($response, $resourceOwner->toArray());
    }

    private function provider(...$args): HeadHunter
    {
        static $default = [
            'clientId'     => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri'  => 'mock_redirect_uri',
        ];

        $values = array_replace($default, ...$args);

        return new HeadHunter($values);
    }

    private function apiUrl(): string
    {
        $getApiUrl = function () {
            return $this->urlApi;
        };

        return $getApiUrl->call($this->provider());
    }
}

function path(string $url): string
{
    return parse_url($url, PHP_URL_PATH);
}

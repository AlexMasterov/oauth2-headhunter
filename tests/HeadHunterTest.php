<?php

namespace AlexMasterov\OAuth2\Client\Tests\Provider;

use AlexMasterov\OAuth2\Client\Provider\Exception\HeadHunterException;
use AlexMasterov\OAuth2\Client\Provider\HeadHunter;
use Eloquent\Phony\Phpunit\Phony;
use GuzzleHttp\ClientInterface;
use League\OAuth2\Client\Token\AccessToken;
use PHPUnit_Framework_TestCase as TestCase;
use Psr\Http\Message\ResponseInterface;

class HeadHunterTest extends TestCase
{
    /**
     * @var HeadHunter
     */
    protected $provider;

    protected function setUp()
    {
        $this->provider = new HeadHunter([
            'clientId'     => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri'  => 'mock_redirect_uri',
        ]);
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testAuthorizationUrl()
    {
        // Run
        $url = $this->provider->getAuthorizationUrl();
        $path = parse_url($url, PHP_URL_PATH);

        // Verify
        $this->assertSame('/oauth/authorize', $path);
    }

    public function testBaseAccessTokenUrl()
    {
        $params = [];

        // Run
        $url = $this->provider->getBaseAccessTokenUrl($params);
        $path = parse_url($url, PHP_URL_PATH);

        // Verify
        $this->assertSame('/oauth/token', $path);
    }

    public function testDefaultScopes()
    {
        $reflection = new \ReflectionClass(get_class($this->provider));
        $getDefaultScopesMethod = $reflection->getMethod('getDefaultScopes');
        $getDefaultScopesMethod->setAccessible(true);

        // Run
        $scope = $getDefaultScopesMethod->invoke($this->provider);

        // Verify
        $this->assertEquals([], $scope);
    }

    public function testGetAccessToken()
    {
        // https://github.com/hhru/api/blob/master/docs/authorization.md
        $rawResponse = [
            'access_token'  => 'mock_access_token',
            'token_type'    => 'bearer',
            'expires_in'    => time() * 3600,
            'refresh_token' => 'mock_refresh_token',
        ];

        $response = Phony::mock(ResponseInterface::class);
        $response->getHeader->with('content-type')->returns('application/json');
        $response->getBody->returns(json_encode($rawResponse));

        $client = Phony::mock(ClientInterface::class);
        $client->send->returns($response->get());

        // Run
        $this->provider->setHttpClient($client->get());
        $token = $this->provider->getAccessToken('authorization_code', [
            'code' => 'mock_authorization_code',
        ]);

        // Verify
        $this->assertEquals($rawResponse['access_token'], $token->getToken());
        $this->assertEquals($rawResponse['refresh_token'], $token->getRefreshToken());
        $this->assertGreaterThanOrEqual($rawResponse['expires_in'], $token->getExpires());
        $this->assertNull($token->getResourceOwnerId());
    }

    public function testUserProperty()
    {
        $rawProperty = [
            'id'          => 12345678,
            'last_name'   => 'lst_name',
            'first_name'  => 'first_name',
            'middle_name' => 'middle_name',
            'email'       => 'email',
        ];

        $response = Phony::mock(ResponseInterface::class);
        $response->getHeader->with('content-type')->returns('application/json');
        $response->getBody->returns(json_encode($rawProperty));

        $client = Phony::mock(ClientInterface::class);
        $client->send->returns($response->get());

        $token = new AccessToken([
            'access_token' => 'mock_access_token',
            'expires_in' => 3600,
        ]);

        // Run
        $this->provider->setHttpClient($client->get());
        $user = $this->provider->getResourceOwner($token);

        // Verify
        $this->assertEquals($rawProperty['id'], $user->getId());
        $this->assertEquals($rawProperty['last_name'], $user->getLastName());
        $this->assertEquals($rawProperty['first_name'], $user->getFirstName());
        $this->assertEquals($rawProperty['middle_name'], $user->getMiddleName());

        $this->assertArrayHasKey('email', $user->toArray());
    }

    public function testErrorResponses()
    {
        $error = [
            'error'             => 'Foo error',
            'error_description' => 'Error description',
        ];

        $message = $error['error'].': '.$error['error_description'];

        $response = Phony::mock(ResponseInterface::class);
        $response->getStatusCode->returns(400);
        $response->getHeader->with('content-type')->returns('application/json');
        $response->getBody->returns(json_encode($error));

        $client = Phony::mock(ClientInterface::class);
        $client->send->returns($response->get());

        // Run
        $this->provider->setHttpClient($client->get());

        $errorMessage = '';
        $errorCode = 0;

        try {
            $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
        } catch (HeadHunterException $e) {
            $errorMessage = $e->getMessage();
            $errorCode = $e->getCode();
            $errorBody = $e->getResponseBody();
        }

        // Verify
        $this->assertEquals($message, $errorMessage);
        $this->assertEquals(400, $errorCode);
        $this->assertEquals(json_encode($error), $errorBody);
    }
}

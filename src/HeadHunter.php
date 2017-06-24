<?php

namespace AlexMasterov\OAuth2\Client\Provider;

use AlexMasterov\OAuth2\Client\Provider\HeadHunterException;
use League\OAuth2\Client\{
    Provider\AbstractProvider,
    Token\AccessToken,
    Tool\BearerAuthorizationTrait
};
use Psr\Http\Message\ResponseInterface;

class HeadHunter extends AbstractProvider
{
    use BearerAuthorizationTrait;

    /**
     * @var string
     */
    protected $urlApi = 'https://api.hh.ru';

    /**
     * @var string
     */
    protected $urlAuthorize = 'https://hh.ru/oauth/authorize';

    /**
     * @var string
     */
    protected $urlAccessToken = 'https://hh.ru/oauth/token';

    /**
     * @var string
     */
    protected $state;

    /**
     * @var string
     */
    protected $redirectUri;

    /**
     * @inheritDoc
     */
    protected function getDefaultScopes()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getBaseAuthorizationUrl()
    {
        return $this->urlAuthorize;
    }

    /**
     * @inheritDoc
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        if (empty($params['code'])) {
            $params['code'] = '';
        }

        return $this->urlAccessToken . '?' .
            $this->buildQueryString($params);
    }

    /**
     * @inheritDoc
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->urlApi . '/me?' .
            $this->buildQueryString([
                'access_token' => (string) $token,
            ]);
    }

    /**
     * @inheritDoc
     */
    protected function getAuthorizationParameters(array $options)
    {
        $options['response_type'] = 'code';
        $options['client_id'] = $this->clientId;

        if (empty($options['state'])) {
            $options['state'] = $this->state;
        }

        if (empty($options['redirect_uri'])) {
            $options['redirect_uri'] = $this->redirectUri;
        }

        return $options;
    }

    /**
     * @inheritDoc
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (isset($data['error'])) {
            throw HeadHunterException::errorResponse($response, $data);
        }
    }

    /**
     * @inheritDoc
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new HeadHunterResourceOwner($response);
    }
}

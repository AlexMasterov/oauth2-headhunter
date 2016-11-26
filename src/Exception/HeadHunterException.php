<?php

namespace AlexMasterov\OAuth2\Client\Provider\Exception;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Psr\Http\Message\ResponseInterface;

class HeadHunterException extends IdentityProviderException
{
    /**
     * @param ResponseInterface $response
     * @param string|array $data
     *
     * @return static
     */
    public static function errorResponse(ResponseInterface $response, $data)
    {
        $message = $data['error'];

        if (!empty($data['error_description'])) {
            $message .= ': '.$data['error_description'];
        }

        $code = $response->getStatusCode();
        $body = (string) $response->getBody();

        return new static($message, $code, $body);
    }
}

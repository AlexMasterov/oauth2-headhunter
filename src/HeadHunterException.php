<?php

namespace AlexMasterov\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Psr\Http\Message\ResponseInterface;

class HeadHunterException extends IdentityProviderException
{
    public static function errorResponse(ResponseInterface $response, $data): HeadHunterException
    {
        $message = $data['error'];

        if (!empty($data['error_description'])) {
            $message .= ': ' . $data['error_description'];
        }

        $code = $response->getStatusCode();
        $body = (string) $response->getBody();

        return new static($message, $code, $body);
    }
}

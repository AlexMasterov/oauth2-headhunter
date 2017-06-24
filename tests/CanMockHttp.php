<?php
declare(strict_types=1);

namespace AlexMasterov\OAuth2\Client\Provider\Tests;

use Psr\Http\Message\ResponseInterface;

trait CanMockHttp
{
    protected function mockResponse(
        string $body = '',
        string $type = 'application/json',
        int $code = 200
    ): ResponseInterface {
        $response = self::createMock(ResponseInterface::class);
        $response->expects(self::any())
            ->method('getHeader')
            ->with(self::stringContains('content-type'))
            ->willReturn($type);
        $response->expects(self::any())
            ->method('getBody')
            ->willReturn($body);
        $response->expects(self::any())
            ->method('getStatusCode')
            ->willReturn($code);

        return $response;
    }
}

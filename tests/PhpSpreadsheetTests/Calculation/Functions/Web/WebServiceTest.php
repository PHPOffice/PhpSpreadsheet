<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Web;

use PhpOffice\PhpSpreadsheet\Calculation\Web;
use PhpOffice\PhpSpreadsheet\Settings;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class WebServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Settings::unsetHttpClient();
    }

    /**
     * @dataProvider providerWEBSERVICE
     */
    public function testWEBSERVICE(string $expectedResult, string $url, ?array $responseData): void
    {
        if ($responseData) {
            $body = $this->createMock(StreamInterface::class);
            $body->expects(self::atMost(1))->method('getContents')->willReturn($responseData[1]);

            $response = $this->createMock(ResponseInterface::class);
            $response->expects(self::once())->method('getStatusCode')->willReturn($responseData[0]);
            $response->expects(self::atMost(1))->method('getBody')->willReturn($body);

            $client = $this->createMock(ClientInterface::class);
            $client->expects(self::once())->method('sendRequest')->willReturn($response);

            $request = $this->createMock(RequestInterface::class);

            $requestFactory = $this->createMock(RequestFactoryInterface::class);
            $requestFactory->expects(self::atMost(1))->method('createRequest')->willReturn($request);

            Settings::setHttpClient($client, $requestFactory);
        }

        $result = Web::WEBSERVICE($url);
        self::assertEquals($expectedResult, $result);
    }

    public function providerWEBSERVICE(): array
    {
        return require 'tests/data/Calculation/Web/WEBSERVICE.php';
    }

    public function testWEBSERVICEReturnErrorWhenClientThrows(): void
    {
        $exception = $this->createMock(\Psr\Http\Client\ClientExceptionInterface::class);

        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())->method('sendRequest')->willThrowException($exception);

        $request = $this->createMock(RequestInterface::class);

        $requestFactory = $this->createMock(RequestFactoryInterface::class);
        $requestFactory->expects(self::atMost(1))->method('createRequest')->willReturn($request);

        Settings::setHttpClient($client, $requestFactory);

        $result = Web::WEBSERVICE('https://example.com');
        self::assertEquals('#VALUE!', $result);
    }

    public function testWEBSERVICEThrowsIfNotClientConfigured(): void
    {
        $this->expectExceptionMessage('HTTP client must be configured via Settings::setHttpClient() to be able to use WEBSERVICE function.');
        Web::WEBSERVICE('https://example.com');
    }
}

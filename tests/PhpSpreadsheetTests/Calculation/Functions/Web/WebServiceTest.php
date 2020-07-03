<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Web;

use Exception;
use GuzzleHttp\Psr7\Response;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Web;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;

class WebServiceTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    public function testSimpleValidCase(): void
    {
        $client = $this->createClientMock();
        $client->response = new Response(200, [], 'Hello world!');
        $result = Web::WEBSERVICE('http://example.com', $client);

        self::assertEquals('Hello world!', $result);
    }

    /**
     * @dataProvider provideInvalidUrls
     */
    public function testInvalidRequestUrls(string $expectedResult, string $url): void
    {
        $client = $this->createClientMock();
        $client->response = new Response(200, [], $expectedResult);
        $result = Web::WEBSERVICE($url, $client);

        self::assertEquals($expectedResult, $result);
    }

    public function testClientExceptionReturnsValueFunction(): void
    {
        $client = $this->createClientMock();
        $client->response = function (): void {
            throw new class() extends Exception implements ClientExceptionInterface {
            };
        };
        $result = Web::WEBSERVICE('http://example.com', $client);

        self::assertEquals('#VALUE!', $result);
    }

    public function testNon200CodeReturnsValueFunction(): void
    {
        $client = $this->createClientMock();
        $client->response = new Response(400, [], '');
        $result = Web::WEBSERVICE('http://example.com', $client);

        self::assertEquals('#VALUE!', $result);
    }

    public function testOutputBodyTooLongReturnsValueFunction(): void
    {
        $client = $this->createClientMock();
        $content = str_repeat('a', 32768);
        $client->response = new Response(200, [], $content);
        $result = Web::WEBSERVICE('http://example.com', $client);

        self::assertEquals('#VALUE!', $result);
    }

    public function provideInvalidUrls(): array
    {
        return require dirname(__DIR__, 5) . '/tests/data/Calculation/Web/WEBSERVICE.php';
    }

    private function createClientMock(): HttpClientMock
    {
        return new HttpClientMock();
    }
}

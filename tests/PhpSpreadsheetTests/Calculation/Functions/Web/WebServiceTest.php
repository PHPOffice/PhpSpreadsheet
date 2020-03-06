<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Web;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Web;
use PhpOffice\PhpSpreadsheet\Settings;
use PHPUnit\Framework\TestCase;

class WebServiceTest extends TestCase
{
    protected static $client;

    public static function setUpBeforeClass(): void
    {
        // Prevent URL requests being sent out
        $mock = new MockHandler([
            new ClientException('This is not a valid URL', new Request('GET', 'test'), new Response()),
            new ConnectException('This is a 404 error', new Request('GET', 'test')),
            new Response('200', [], str_repeat('a', 40000)),
            new Response('200', [], 'This is a test'),
        ]);

        $handlerStack = HandlerStack::create($mock);
        self::$client = new Client(['handler' => $handlerStack]);
    }

    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerWEBSERVICE
     */
    public function testWEBSERVICE(string $expectedResult, string $url): void
    {
        Settings::setHttpClient(self::$client);
        $result = Web::WEBSERVICE($url);
        self::assertEquals($expectedResult, $result);
    }

    public function providerWEBSERVICE(): array
    {
        return require 'tests/data/Calculation/Web/WEBSERVICE.php';
    }
}

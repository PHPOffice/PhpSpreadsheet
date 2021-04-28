<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Web;

use PhpOffice\PhpSpreadsheet\Calculation\Web\Service;
use PHPUnit\Framework\TestCase;

class UrlEncodeTest extends TestCase
{
    /**
     * @dataProvider providerURLENCODE
     */
    public function testURLENCODE(string $expectedResult, string $text): void
    {
        $result = Service::urlEncode($text);
        self::assertSame($expectedResult, $result);
    }

    public function providerURLENCODE(): array
    {
        return require 'tests/data/Calculation/Web/URLENCODE.php';
    }
}

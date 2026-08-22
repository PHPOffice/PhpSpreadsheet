<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Web;

use PhpOffice\PhpSpreadsheet\Calculation\Web\Service;
use PHPUnit\Framework\TestCase;

class UrlEncodeTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('providerURLENCODE')]
    public function testURLENCODE(string $expectedResult, mixed $text): void
    {
        $result = Service::urlEncode($text);
        self::assertSame($expectedResult, $result);
    }

    public static function providerURLENCODE(): array
    {
        return require 'tests/data/Calculation/Web/URLENCODE.php';
    }
}

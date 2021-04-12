<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerINDEX
     *
     * @param mixed $expectedResult
     */
    public function testINDEX($expectedResult, ...$args): void
    {
        $result = LookupRef::INDEX(...$args);
//        var_dump($result);
        self::assertEquals($expectedResult, $result);
    }

    public function providerINDEX(): array
    {
        return require 'tests/data/Calculation/LookupRef/INDEX.php';
    }
}

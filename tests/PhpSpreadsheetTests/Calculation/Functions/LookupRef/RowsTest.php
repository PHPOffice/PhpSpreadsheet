<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PHPUnit\Framework\TestCase;

class RowsTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerROWS
     *
     * @param mixed $expectedResult
     */
    public function testROWS($expectedResult, ...$args): void
    {
        $result = LookupRef::ROWS(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerROWS(): array
    {
        return require 'tests/data/Calculation/LookupRef/ROWS.php';
    }
}

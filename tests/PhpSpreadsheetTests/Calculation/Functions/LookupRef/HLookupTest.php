<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PHPUnit\Framework\TestCase;

class HLookupTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerHLOOKUP
     *
     * @param mixed $expectedResult
     */
    public function testHLOOKUP($expectedResult, ...$args): void
    {
        $result = LookupRef::HLOOKUP(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerHLOOKUP(): array
    {
        return require 'tests/data/Calculation/LookupRef/HLOOKUP.php';
    }
}

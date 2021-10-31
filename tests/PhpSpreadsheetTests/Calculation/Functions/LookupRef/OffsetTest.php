<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PHPUnit\Framework\TestCase;

class OffsetTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerOFFSET
     *
     * @param mixed $expectedResult
     * @param null|mixed $cellReference
     */
    public function testOFFSET($expectedResult, $cellReference = null): void
    {
        $result = LookupRef::OFFSET($cellReference);
        self::assertSame($expectedResult, $result);
    }

    public function providerOFFSET(): array
    {
        return require 'tests/data/Calculation/LookupRef/OFFSET.php';
    }
}

<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PHPUnit\Framework\TestCase;

class IndirectTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerINDIRECT
     *
     * @param mixed $expectedResult
     * @param null|mixed $cellReference
     */
    public function testINDIRECT($expectedResult, $cellReference = null): void
    {
        $result = LookupRef::INDIRECT($cellReference);
        self::assertSame($expectedResult, $result);
    }

    public function providerINDIRECT()
    {
        return require 'tests/data/Calculation/LookupRef/INDIRECT.php';
    }
}

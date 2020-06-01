<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class FisherInvTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerFISHERINV
     *
     * @param mixed $expectedResult
     * @param $value
     */
    public function testFISHERINV($expectedResult, $value): void
    {
        $result = Statistical::FISHERINV($value);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerFISHERINV()
    {
        return require 'tests/data/Calculation/Statistical/FISHERINV.php';
    }
}

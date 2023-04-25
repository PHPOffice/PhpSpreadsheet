<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PHPUnit\Framework\TestCase;

class TransposeTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerTRANSPOSE
     *
     * @param mixed $expectedResult
     * @param mixed $matrix
     */
    public function testTRANSPOSE($expectedResult, $matrix): void
    {
        $result = LookupRef\Matrix::transpose($matrix);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerTRANSPOSE(): array
    {
        return require 'tests/data/Calculation/LookupRef/TRANSPOSE.php';
    }
}

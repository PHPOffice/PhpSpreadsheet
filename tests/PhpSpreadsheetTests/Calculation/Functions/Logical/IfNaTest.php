<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Logical;
use PHPUnit\Framework\TestCase;

class IfNaTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerIFNA
     *
     * @param mixed $expectedResult
     * @param mixed $value
     * @param mixed $return
     */
    public function testIFNA($expectedResult, $value, $return): void
    {
        $result = Logical::IFNA($value, $return);
        self::assertEquals($expectedResult, $result);
    }

    public function providerIFNA(): array
    {
        return require 'tests/data/Calculation/Logical/IFNA.php';
    }
}

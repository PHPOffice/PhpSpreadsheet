<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Logical;
use PHPUnit\Framework\TestCase;

class IfErrorTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerIFERROR
     *
     * @param mixed $expectedResult
     * @param mixed $value
     * @param mixed $return
     */
    public function testIFERROR($expectedResult, $value, $return): void
    {
        $result = Logical::IFERROR($value, $return);
        self::assertEquals($expectedResult, $result);
    }

    public function providerIFERROR(): array
    {
        return require 'tests/data/Calculation/Logical/IFERROR.php';
    }
}

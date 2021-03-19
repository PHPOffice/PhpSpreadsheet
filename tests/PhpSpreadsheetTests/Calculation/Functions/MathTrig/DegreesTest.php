<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class DegreesTest extends TestCase
{
    /**
     * @dataProvider providerDEGREES
     *
     * @param mixed $expectedResult
     * @param mixed $val
     */
    public function testDEGREES($expectedResult, $val = null): void
    {
        if ($val === null) {
            $this->expectException(CalcExp::class);
            $formula = '=DEGREES()';
        } else {
            $formula = "=DEGREES($val)";
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue($formula);
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public function providerDegrees()
    {
        return require 'tests/data/Calculation/MathTrig/DEGREES.php';
    }
}

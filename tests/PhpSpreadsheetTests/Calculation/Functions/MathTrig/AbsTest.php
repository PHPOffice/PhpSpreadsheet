<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class AbsTest extends TestCase
{
    /**
     * @dataProvider providerAbs
     *
     * @param mixed $expectedResult
     * @param mixed $val
     */
    public function testRound($expectedResult, $val = null): void
    {
        if ($val === null) {
            $this->expectException(CalcExp::class);
            $formula = '=ABS()';
        } else {
            $formula = "=ABS($val)";
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue($formula);
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerAbs()
    {
        return require 'tests/data/Calculation/MathTrig/ABS.php';
    }
}

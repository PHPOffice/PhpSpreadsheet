<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class RoundTest extends TestCase
{
    /**
     * @dataProvider providerRound
     *
     * @param mixed $expectedResult
     * @param mixed $val
     * @param mixed $precision
     */
    public function testRound($expectedResult, $val = null, $precision = null): void
    {
        if ($val === null) {
            $this->expectException(CalcExp::class);
            $formula = '=ROUND()';
        } elseif ($precision === null) {
            $this->expectException(CalcExp::class);
            $formula = "=ROUND($val)";
        } else {
            $formula = "=ROUND($val, $precision)";
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue($formula);
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerRound()
    {
        return require 'tests/data/Calculation/MathTrig/ROUND.php';
    }
}

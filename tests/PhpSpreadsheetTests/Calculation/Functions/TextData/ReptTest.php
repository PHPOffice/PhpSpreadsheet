<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class ReptTest extends TestCase
{
    /**
     * @dataProvider providerREPT
     *
     * @param mixed $expectedResult
     * @param mixed $val
     * @param mixed $rpt
     */
    public function testRound($expectedResult, $val = null, $rpt = null): void
    {
        if ($val === null) {
            $this->expectException(CalcExp::class);
            $formula = '=REPT()';
        } elseif ($rpt === null) {
            $this->expectException(CalcExp::class);
            $formula = "=REPT($val)";
        } else {
            $formula = "=REPT($val, $rpt)";
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue($formula);
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerREPT()
    {
        return require 'tests/data/Calculation/TextData/REPT.php';
    }
}

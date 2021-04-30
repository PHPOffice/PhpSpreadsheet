<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Calculation\TextData;
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
    public function testReptDirect($expectedResult, $val = null, $rpt = null): void
    {
        $result = TextData::builtinREPT(is_string($val) ? trim($val, '"') : $val, $rpt);
        self::assertEquals($expectedResult, $result);
    }

    /**
     * @dataProvider providerREPT
     *
     * @param mixed $expectedResult
     * @param mixed $val
     * @param mixed $rpt
     */
    public function testReptThroughEngine($expectedResult, $val = null, $rpt = null): void
    {
        if ($val === null) {
            $this->expectException(CalcExp::class);
            $formula = '=REPT()';
        } elseif ($rpt === null) {
            $this->expectException(CalcExp::class);
            $formula = "=REPT($val)";
        } else {
            if (is_bool($val)) {
                $val = ($val) ? Calculation::getTRUE() : Calculation::getFALSE();
            }
            $formula = "=REPT($val, $rpt)";
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue($formula);
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerREPT(): array
    {
        return require 'tests/data/Calculation/TextData/REPT.php';
    }
}

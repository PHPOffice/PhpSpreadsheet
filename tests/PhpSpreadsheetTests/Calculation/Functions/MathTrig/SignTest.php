<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class SignTest extends TestCase
{
    /**
     * @dataProvider providerSIGN
     *
     * @param mixed $expectedResult
     * @param $value
     */
    public function testSIGN($expectedResult, $value): void
    {
        if ($expectedResult === 'exception') {
            $this->expectException(CalcExp::class);
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A2', 1.3);
        $sheet->setCellValue('A3', 0);
        $sheet->setCellValue('A4', -3.8);
        $sheet->getCell('A1')->setValue("=SIGN($value)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerSIGN()
    {
        return require 'tests/data/Calculation/MathTrig/SIGN.php';
    }
}

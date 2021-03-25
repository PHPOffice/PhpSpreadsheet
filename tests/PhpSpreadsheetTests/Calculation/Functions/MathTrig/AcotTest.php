<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class AcotTest extends TestCase
{
    /**
     * @dataProvider providerACOT
     *
     * @param mixed $expectedResult
     * @param mixed $number
     */
    public function testACOT($expectedResult, $number): void
    {
        if ($expectedResult === 'exception') {
            $this->expectException(CalcExp::class);
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A2', 1.3);
        $sheet->setCellValue('A3', 2.7);
        $sheet->setCellValue('A4', -3.8);
        $sheet->setCellValue('A5', -5);
        $sheet->getCell('A1')->setValue("=ACOT($number)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-9);
    }

    public function providerACOT()
    {
        return require 'tests/data/Calculation/MathTrig/ACOT.php';
    }
}

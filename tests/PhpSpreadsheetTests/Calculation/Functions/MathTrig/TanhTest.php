<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class TanhTest extends TestCase
{
    /**
     * @dataProvider providerTanh
     *
     * @param mixed $expectedResult
     */
    public function testTanh($expectedResult, string $formula): void
    {
        if ($expectedResult === 'exception') {
            $this->expectException(CalcExp::class);
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A2', 1);
        $sheet->getCell('A1')->setValue("=TANH($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public function providerTanh()
    {
        return require 'tests/data/Calculation/MathTrig/TANH.php';
    }
}

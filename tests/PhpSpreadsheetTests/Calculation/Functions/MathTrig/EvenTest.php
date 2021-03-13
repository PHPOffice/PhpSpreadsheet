<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class EvenTest extends TestCase
{
    /**
     * @dataProvider providerEVEN
     *
     * @param mixed $expectedResult
     * @param $value
     */
    public function testEVEN($expectedResult, $value): void
    {
        if ($expectedResult === 'exception') {
            $this->expectException(CalcExp::class);
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue("=EVEN($value)");
        $sheet->getCell('A2')->setValue(3.7);
        self::assertEquals($expectedResult, $sheet->getCell('A1')->getCalculatedValue());
    }

    public function providerEVEN()
    {
        return require 'tests/data/Calculation/MathTrig/EVEN.php';
    }
}

<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class Atan2Test extends TestCase
{
    /**
     * @dataProvider providerATAN2
     *
     * @param mixed $expectedResult
     */
    public function testATAN2($expectedResult, string $formula): void
    {
        if ($expectedResult === 'exception') {
            $this->expectException(CalcExp::class);
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A2')->setValue(5);
        $sheet->getCell('A3')->setValue(6);
        $sheet->getCell('A1')->setValue("=ATAN2($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-9);
    }

    public function providerATAN2()
    {
        return require 'tests/data/Calculation/MathTrig/ATAN2.php';
    }
}

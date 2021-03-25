<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class AtanhTest extends TestCase
{
    /**
     * @dataProvider providerAtanh
     *
     * @param mixed $expectedResult
     */
    public function testAtanh($expectedResult, string $formula): void
    {
        if ($expectedResult === 'exception') {
            $this->expectException(CalcExp::class);
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A2')->setValue(0.8);
        $sheet->getCell('A1')->setValue("=ATANH($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public function providerAtanh()
    {
        return require 'tests/data/Calculation/MathTrig/ATANH.php';
    }
}

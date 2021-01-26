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
     * @param mixed $val
     */
    public function testAtan($expectedResult, $val = null): void
    {
        if ($val === null) {
            $this->expectException(CalcExp::class);
            $formula = '=ATANH()';
        } else {
            $formula = "=ATANH($val)";
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue($formula);
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public function providerAtanh()
    {
        return require 'tests/data/Calculation/MathTrig/ATANH.php';
    }
}

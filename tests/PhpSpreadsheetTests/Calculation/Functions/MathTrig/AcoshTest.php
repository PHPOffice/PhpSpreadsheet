<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class AcoshTest extends TestCase
{
    /**
     * @dataProvider providerAcosh
     *
     * @param mixed $expectedResult
     * @param mixed $val
     */
    public function testAcosh($expectedResult, $val = null): void
    {
        if ($val === null) {
            $this->expectException(CalcExp::class);
            $formula = '=ACOSH()';
        } else {
            $formula = "=ACOSH($val)";
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue($formula);
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public function providerAcosh()
    {
        return require 'tests/data/Calculation/MathTrig/ACOSH.php';
    }
}

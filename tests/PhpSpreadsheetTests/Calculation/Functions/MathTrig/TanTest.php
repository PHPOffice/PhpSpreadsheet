<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class TanTest extends TestCase
{
    /**
     * @dataProvider providerTan
     *
     * @param mixed $expectedResult
     * @param mixed $val
     */
    public function testTan($expectedResult, $val = null): void
    {
        if ($val === null) {
            $this->expectException(CalcExp::class);
            $formula = '=TAN()';
        } else {
            $formula = "=TAN($val)";
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue($formula);
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public function providerTan()
    {
        return require 'tests/data/Calculation/MathTrig/TAN.php';
    }

    /**
     * Php returns very large number (pos or neg) rather than infinity.
     */
    public function testTanInfinite(): void
    {
        $formula = '=TAN(PI()/2)';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue($formula);
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertTrue(abs((float) $result) > 1E15);
    }
}

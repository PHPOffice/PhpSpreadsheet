<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class CoshTest extends TestCase
{
    /**
     * @dataProvider providerCosh
     *
     * @param mixed $expectedResult
     */
    public function testCosh($expectedResult, string $formula): void
    {
        if ($expectedResult === 'exception') {
            $this->expectException(CalcExp::class);
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A2', 2);
        $sheet->getCell('A1')->setValue("=COSH($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public function providerCosh()
    {
        return require 'tests/data/Calculation/MathTrig/COSH.php';
    }
}

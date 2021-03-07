<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class AcosTest extends TestCase
{
    /**
     * @dataProvider providerAcos
     *
     * @param mixed $expectedResult
     */
    public function testAcos($expectedResult, string $formula): void
    {
        if ($expectedResult === 'exception') {
            $this->expectException(CalcExp::class);
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A2')->setValue(0.5);
        $sheet->getCell('A1')->setValue("=ACOS($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public function providerAcos()
    {
        return require 'tests/data/Calculation/MathTrig/ACOS.php';
    }
}

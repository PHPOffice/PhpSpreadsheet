<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class RomanTest extends TestCase
{
    /**
     * @dataProvider providerROMAN
     *
     * @param mixed $expectedResult
     * @param mixed $formula
     */
    public function testROMAN($expectedResult, $formula): void
    {
        if ($expectedResult === 'exception') {
            $this->expectException(CalcExp::class);
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A3', 49);
        $sheet->getCell('A1')->setValue("=ROMAN($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerROMAN()
    {
        return require 'tests/data/Calculation/MathTrig/ROMAN.php';
    }

    // Confirm that deprecated stub left in MathTrig works.
    // Delete this test when stub is finally deleted.
    public function testDeprecated(): void
    {
        self::assertEquals('I', \PhpOffice\PhpSpreadsheet\Calculation\MathTrig::ROMAN(1));
    }
}

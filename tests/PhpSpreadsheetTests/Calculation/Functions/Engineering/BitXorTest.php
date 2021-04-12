<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class BitXorTest extends TestCase
{
    /**
     * @dataProvider providerBITXOR
     *
     * @param mixed $expectedResult
     */
    public function testBITXOR($expectedResult, string $formula): void
    {
        if ($expectedResult === 'exception') {
            $this->expectException(CalcExp::class);
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A2', 8);
        $sheet->getCell('A1')->setValue("=BITXOR($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerBITXOR(): array
    {
        return require 'tests/data/Calculation/Engineering/BITXOR.php';
    }
}

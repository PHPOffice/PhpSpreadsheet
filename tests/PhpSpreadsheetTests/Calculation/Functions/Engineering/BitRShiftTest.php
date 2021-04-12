<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class BitRShiftTest extends TestCase
{
    /**
     * @dataProvider providerBITRSHIFT
     *
     * @param mixed $expectedResult
     */
    public function testBITRSHIFT($expectedResult, string $formula): void
    {
        if ($expectedResult === 'exception') {
            $this->expectException(CalcExp::class);
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A2', 8);
        $sheet->getCell('A1')->setValue("=BITRSHIFT($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerBITRSHIFT(): array
    {
        return require 'tests/data/Calculation/Engineering/BITRSHIFT.php';
    }
}

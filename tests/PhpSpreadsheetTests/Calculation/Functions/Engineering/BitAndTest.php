<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class BitAndTest extends TestCase
{
    /**
     * @dataProvider providerBITAND
     *
     * @param mixed $expectedResult
     */
    public function testBITAND($expectedResult, string $formula, bool $skip32 = false): void
    {
        if ($skip32 && PHP_INT_SIZE === 4) {
            self::markTestSkipped('Test invalid on 32-bit system.');
        }
        if ($expectedResult === 'exception') {
            $this->expectException(CalcExp::class);
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A2', 24);
        $sheet->getCell('A1')->setValue("=BITAND($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerBITAND()
    {
        return require 'tests/data/Calculation/Engineering/BITAND.php';
    }
}

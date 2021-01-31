<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class RadiansTest extends TestCase
{
    /**
     * @dataProvider providerRADIANS
     *
     * @param mixed $expectedResult
     * @param mixed $val
     */
    public function testRADIANS($expectedResult, $val = null): void
    {
        if ($val === null) {
            $this->expectException(CalcExp::class);
            $formula = '=RADIANS()';
        } else {
            $formula = "=RADIANS($val)";
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue($formula);
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public function providerRADIANS()
    {
        return require 'tests/data/Calculation/MathTrig/RADIANS.php';
    }
}

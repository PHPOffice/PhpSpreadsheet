<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class CeilingTest extends TestCase
{
    private $compatibilityMode;

    protected function setUp(): void
    {
        $this->compatibilityMode = Functions::getCompatibilityMode();
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    protected function tearDown(): void
    {
        Functions::setCompatibilityMode($this->compatibilityMode);
    }

    /**
     * @dataProvider providerCEILING
     *
     * @param mixed $expectedResult
     * @param string $formula
     */
    public function testCEILING($expectedResult, $formula): void
    {
        if ($expectedResult === 'exception') {
            $this->expectException(CalcExp::class);
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A2', 1.3);
        $sheet->setCellValue('A3', 2.7);
        $sheet->setCellValue('A4', -3.8);
        $sheet->setCellValue('A5', -5.2);
        $sheet->getCell('A1')->setValue("=CEILING($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerCEILING()
    {
        return require 'tests/data/Calculation/MathTrig/CEILING.php';
    }

    public function testCEILINGGnumeric1Arg(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_GNUMERIC);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue('=CEILING(5.1)');
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta(6, $result, 1E-12);
    }

    public function testCELINGOpenOffice1Arg(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue('=CEILING(5.1)');
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta(6, $result, 1E-12);
    }

    public function testFLOORExcel1Arg(): void
    {
        $this->expectException(CalcExp::class);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        $sheet->getCell('A1')->setValue('=CEILING(5.1)');
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta(6, $result, 1E-12);
    }
}

<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class Bin2OctTest extends TestCase
{
    /**
     * @var string
     */
    private $compatibilityMode;

    protected function setUp(): void
    {
        $this->compatibilityMode = Functions::getCompatibilityMode();
    }

    protected function tearDown(): void
    {
        Functions::setCompatibilityMode($this->compatibilityMode);
    }

    /**
     * @dataProvider providerBIN2OCT
     *
     * @param mixed $expectedResult
     * @param mixed $formula
     */
    public function testBin2Oct($expectedResult, $formula): void
    {
        if ($expectedResult === 'exception') {
            $this->expectException(CalcExp::class);
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A2', 101);
        $sheet->getCell('A1')->setValue("=BIN2OCT($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerBIN2OCT(): array
    {
        return require 'tests/data/Calculation/Engineering/BIN2OCT.php';
    }

    /**
     * @dataProvider providerBIN2OCT
     *
     * @param mixed $expectedResult
     * @param mixed $formula
     */
    public function testBIN2OCTOds($expectedResult, $formula): void
    {
        if ($expectedResult === 'exception') {
            $this->expectException(CalcExp::class);
        }
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);
        if ($formula === 'true') {
            $expectedResult = 1;
        } elseif ($formula === 'false') {
            $expectedResult = 0;
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A2', 101);
        $sheet->getCell('A1')->setValue("=BIN2OCT($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function testBIN2OCTFrac(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_GNUMERIC);
        $cell = 'G1';
        $sheet->setCellValue($cell, '=BIN2OCT(101.1)');
        self::assertEquals(5, $sheet->getCell($cell)->getCalculatedValue());
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);
        $cell = 'O1';
        $sheet->setCellValue($cell, '=BIN2OCT(101.1)');
        self::assertEquals('#NUM!', $sheet->getCell($cell)->getCalculatedValue());
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        $cell = 'E1';
        $sheet->setCellValue($cell, '=BIN2OCT(101.1)');
        self::assertEquals('#NUM!', $sheet->getCell($cell)->getCalculatedValue());
    }
}

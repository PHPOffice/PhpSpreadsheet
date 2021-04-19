<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class Dec2BinTest extends TestCase
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
     * @dataProvider providerDEC2BIN
     *
     * @param mixed $expectedResult
     * @param mixed $formula
     */
    public function testDEC2BIN($expectedResult, $formula): void
    {
        if ($expectedResult === 'exception') {
            $this->expectException(CalcExp::class);
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A2', 5);
        $sheet->getCell('A1')->setValue("=DEC2BIN($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerDEC2BIN(): array
    {
        return require 'tests/data/Calculation/Engineering/DEC2BIN.php';
    }

    /**
     * @dataProvider providerDEC2BIN
     *
     * @param mixed $expectedResult
     * @param mixed $formula
     */
    public function testDEC2BINOds($expectedResult, $formula): void
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
        $sheet->setCellValue('A2', 5);
        $sheet->getCell('A1')->setValue("=DEC2BIN($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function testDEC2BINFrac(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_GNUMERIC);
        $cell = 'G1';
        $sheet->setCellValue($cell, '=DEC2BIN(5.1)');
        self::assertEquals(101, $sheet->getCell($cell)->getCalculatedValue(), 'Gnumeric');
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);
        $cell = 'O1';
        $sheet->setCellValue($cell, '=DEC2BIN(5.1)');
        self::assertEquals(101, $sheet->getCell($cell)->getCalculatedValue(), 'Ods');
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        $cell = 'E1';
        $sheet->setCellValue($cell, '=DEC2BIN(5.1)');
        self::assertEquals(101, $sheet->getCell($cell)->getCalculatedValue(), 'Excel');
    }
}

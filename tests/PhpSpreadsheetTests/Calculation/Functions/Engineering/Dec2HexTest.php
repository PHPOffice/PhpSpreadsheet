<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class Dec2HexTest extends TestCase
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
     * @dataProvider providerDEC2HEX
     *
     * @param mixed $expectedResult
     * @param mixed $formula
     */
    public function testDEC2HEX($expectedResult, $formula): void
    {
        if ($expectedResult === 'exception') {
            $this->expectException(CalcExp::class);
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A2', 17);
        $sheet->getCell('A1')->setValue("=DEC2HEX($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerDEC2HEX(): array
    {
        return require 'tests/data/Calculation/Engineering/DEC2HEX.php';
    }

    /**
     * @dataProvider providerDEC2HEX
     *
     * @param mixed $expectedResult
     * @param mixed $formula
     */
    public function testDEC2HEXOds($expectedResult, $formula): void
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
        $sheet->setCellValue('A2', 17);
        $sheet->getCell('A1')->setValue("=DEC2HEX($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function testDEC2HEXFrac(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_GNUMERIC);
        $cell = 'G1';
        $sheet->setCellValue($cell, '=DEC2HEX(17.1)');
        self::assertEquals(11, $sheet->getCell($cell)->getCalculatedValue(), 'Gnumeric');
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);
        $cell = 'O1';
        $sheet->setCellValue($cell, '=DEC2HEX(17.1)');
        self::assertEquals(11, $sheet->getCell($cell)->getCalculatedValue(), 'Ods');
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        $cell = 'E1';
        $sheet->setCellValue($cell, '=DEC2HEX(17.1)');
        self::assertEquals(11, $sheet->getCell($cell)->getCalculatedValue(), 'Excel');
    }

    public function test32bitHex(): void
    {
        self::assertEquals('A2DE246000', Engineering\ConvertDecimal::hex32bit(-400000000000, 'DE246000', true));
        self::assertEquals('7FFFFFFFFF', Engineering\ConvertDecimal::hex32bit(549755813887, 'FFFFFFFF', true));
        self::assertEquals('FFFFFFFFFF', Engineering\ConvertDecimal::hex32bit(-1, 'FFFFFFFF', true));
    }
}

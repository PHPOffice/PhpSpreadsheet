<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Gnumeric;

use PhpOffice\PhpSpreadsheet\Reader\Gnumeric;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class ArrayFormulaTest extends TestCase
{
    /**
     * @var Spreadsheet
     */
    private $spreadsheet;

    protected function setUp(): void
    {
        $filename = 'tests/data/Reader/Gnumeric/ArrayFormulaTest.gnumeric';
        $reader = new Gnumeric();
        $this->spreadsheet = $reader->load($filename);
    }

    /**
     * @dataProvider arrayFormulaReaderProvider
     */
    public function testArrayFormulaReader(
        string $cellAddress,
        string $expectedRange,
        string $expectedFormula,
        array $expectedValue
    ): void {
        $worksheet = $this->spreadsheet->getActiveSheet();

        $cell = $worksheet->getCell($cellAddress);
        self::assertTrue($cell->isArrayFormula());
        self::assertSame($expectedRange, $cell->arrayFormulaRange());
        self::assertSame($expectedFormula, $cell->getValue());
        self::assertSame($expectedValue, $cell->getCalculatedValue(true, true));
//        self::assertSame(8, $cell->getCalculatedValue());
//        self::assertSame(8, $cell->getCalculatedValue());
//        self::assertSame(12, $worksheet->getCell('C2')->getCalculatedValue());
//        self::assertSame(10, $worksheet->getCell('B3')->getCalculatedValue());
//        self::assertSame(15, $worksheet->getCell('C3')->getCalculatedValue());
    }

    public function arrayFormulaReaderProvider(): array
    {
        return [
            [
                'D1',
                'D1:E2',
                '=A1:B1*A1:A2',
                [[4, 6], [8, 12]],
            ],
            [
                'D4',
                'D4:E5',
                '=A4:B4*A4:A5',
                [[9, 12], [15, 20]],
            ],
            [
                'D7',
                'D7:E8',
                '=A7:B7*A7:A8',
                [[16, 20], [24, 30]],
            ],
            [
                'D10',
                'D10:E11',
                '=A10:B10*A10:A11',
                [[25, 30], [35, 42]],
            ],
            [
                'D13',
                'D13:E14',
                '=A13:B13*A13:A14',
                [[36, 42], [48, 56]],
            ],
        ];
    }
}

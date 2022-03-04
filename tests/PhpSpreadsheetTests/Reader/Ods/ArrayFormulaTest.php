<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Reader\Ods;
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
        $filename = 'tests/data/Reader/Ods/ArrayFormulaTest.ods';
        $reader = new Ods();
        $this->spreadsheet = $reader->load($filename);
    }

    public function testArrayFormulaReader(): void
    {
        $worksheet = $this->spreadsheet->getActiveSheet();

        $cell = $worksheet->getCell('B2');
        self::assertTrue($cell->isArrayFormula());
        self::assertSame('B2:C3', $cell->arrayFormulaRange());
        self::assertSame('={2,3}*{4;5}', $cell->getValue());
        self::assertSame([[8, 12], [10, 15]], $cell->getCalculatedValue(true, true));
        self::assertSame(8, $cell->getCalculatedValue());
        self::assertSame(8, $cell->getCalculatedValue());
        self::assertSame(12, $worksheet->getCell('C2')->getCalculatedValue());
        self::assertSame(10, $worksheet->getCell('B3')->getCalculatedValue());
        self::assertSame(15, $worksheet->getCell('C3')->getCalculatedValue());
    }
}

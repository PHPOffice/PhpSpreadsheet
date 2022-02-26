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

    public function testAutoFilterRange(): void
    {
        $worksheet = $this->spreadsheet->getActiveSheet();

        $cell = $worksheet->getCell('B2');
        self::assertTrue($cell->isArrayFormula());
        self::assertSame('B2:C3', $cell->arrayFormulaRange());
    }
}

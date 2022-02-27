<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
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
        Calculation::getInstance($this->spreadsheet)->flushInstance();
        self::assertSame('={2,3}*{4;5}', $cell->getValue());
    }
}

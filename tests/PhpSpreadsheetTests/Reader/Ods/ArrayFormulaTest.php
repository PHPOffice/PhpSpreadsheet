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
        var_dump($worksheet->getCell('B2')->getValue());
//        var_dump($worksheet->getCell('B2')->getCalculatedValue(true, true)); // Is currently resetting the range to NULLs
        var_dump($worksheet->getCell('C2')->getValue());
//        var_dump($worksheet->getCell('C2')->getCalculatedValue());
        var_dump($worksheet->getCell('B3')->getValue());
//        var_dump($worksheet->getCell('B3')->getCalculatedValue());
        var_dump($worksheet->getCell('C3')->getValue());
//        var_dump($worksheet->getCell('C3')->getCalculatedValue());
    }
}

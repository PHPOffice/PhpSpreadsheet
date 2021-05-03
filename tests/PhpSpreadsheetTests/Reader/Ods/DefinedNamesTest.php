<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class DefinedNamesTest extends TestCase
{
    /**
     * @var Spreadsheet
     */
    private $spreadsheet;

    protected function setUp(): void
    {
        $filename = 'tests/data/Reader/Ods/DefinedNames.ods';
        $reader = new Ods();
        $this->spreadsheet = $reader->load($filename);
    }

    public function testDefinedNames(): void
    {
        $worksheet = $this->spreadsheet->getActiveSheet();

        $firstDefinedNameValue = $worksheet->getCell('First')->getValue();
        $secondDefinedNameValue = $worksheet->getCell('Second')->getValue();
        $calculatedFormulaValue = $worksheet->getCell('B2')->getCalculatedValue();

        self::assertSame(3, $firstDefinedNameValue);
        self::assertSame(4, $secondDefinedNameValue);
        self::assertSame(12, $calculatedFormulaValue);
    }
}

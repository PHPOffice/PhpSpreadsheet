<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PHPUnit\Framework\TestCase;

class DefinedNamesTest extends TestCase
{
    public function testDefinedNames(): void
    {
        $filename = 'tests/data/Reader/Ods/DefinedNames.ods';
        $reader = new Ods();
        $spreadsheet = $reader->load($filename);
        $worksheet = $spreadsheet->getActiveSheet();

        $firstDefinedNameValue = $worksheet->getCell('First')->getValue();
        $secondDefinedNameValue = $worksheet->getCell('Second')->getValue();
        $calculatedFormulaValue = $worksheet->getCell('B2')->getCalculatedValue();

        self::assertSame(3, $firstDefinedNameValue);
        self::assertSame(4, $secondDefinedNameValue);
        self::assertSame(12, $calculatedFormulaValue);
        $spreadsheet->disconnectWorksheets();
    }
}

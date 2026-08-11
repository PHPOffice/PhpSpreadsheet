<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PHPUnit\Framework\TestCase;

class DefinedNamesTest extends TestCase
{
    public function testDefinedNamesValue(): void
    {
        $filename = 'tests/data/Reader/Ods/DefinedNames.ods';
        $reader = new Ods();
        $spreadsheet = $reader->load($filename);
        $spreadsheet->returnArrayAsValue();
        $worksheet = $spreadsheet->getActiveSheet();

        $firstDefinedNameValue = $worksheet->getCell('First')->getValue();
        $secondDefinedNameValue = $worksheet->getCell('Second')->getValue();
        $calculatedFormulaValue = $worksheet->getCell('B2')->getCalculatedValue();

        self::assertSame(3, $firstDefinedNameValue);
        self::assertSame(4, $secondDefinedNameValue);
        self::assertSame(12, $calculatedFormulaValue);
        $spreadsheet->disconnectWorksheets();
    }

    public function testDefinedNamesApostropheValue(): void
    {
        $filename = 'tests/data/Reader/Ods/DefinedNames.apostrophe.ods';
        $reader = new Ods();
        $spreadsheet = $reader->load($filename);
        $spreadsheet->returnArrayAsValue();
        $worksheet = $spreadsheet->getActiveSheet();
        self::assertSame("apo'strophe", $worksheet->getTitle());

        $firstDefinedNameValue = $worksheet->getCell('First')->getValue();
        $secondDefinedNameValue = $worksheet->getCell('Second')->getValue();
        $calculatedFormulaValue = $worksheet->getCell('B2')->getCalculatedValue();

        self::assertSame(3, $firstDefinedNameValue);
        self::assertSame(4, $secondDefinedNameValue);
        self::assertSame(12, $calculatedFormulaValue);
        $spreadsheet->disconnectWorksheets();
    }

    public function testDefinedNamesArray(): void
    {
        $filename = 'tests/data/Reader/Ods/DefinedNames.ods';
        $reader = new Ods();
        $spreadsheet = $reader->load($filename);
        $spreadsheet->returnArrayAsArray();
        $worksheet = $spreadsheet->getActiveSheet();

        $firstDefinedNameValue = $worksheet->getCell('First')->getValue();
        $secondDefinedNameValue = $worksheet->getCell('Second')->getValue();
        $calculatedFormulaValue = $worksheet->getCell('B2')->getCalculatedValue();

        self::assertSame(3, $firstDefinedNameValue);
        self::assertSame(4, $secondDefinedNameValue);
        self::assertSame([12], $calculatedFormulaValue);
        $spreadsheet->disconnectWorksheets();
    }
}

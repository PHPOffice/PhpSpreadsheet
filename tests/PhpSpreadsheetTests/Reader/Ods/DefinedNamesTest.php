<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PHPUnit\Framework\TestCase;

class DefinedNamesTest extends TestCase
{
    public function testDefinedNamesValue(): void
    {
        $filename = 'tests/data/Reader/Ods/DefinedNames.ods';
        $reader = new Ods();
        $spreadsheet = $reader->load($filename);
        $calculation = Calculation::getInstance($spreadsheet);
        $calculation->setInstanceArrayReturnType(
            Calculation::RETURN_ARRAY_AS_VALUE
        );
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
        $calculation = Calculation::getInstance($spreadsheet);
        $calculation->setInstanceArrayReturnType(
            Calculation::RETURN_ARRAY_AS_VALUE
        );
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
        $calculation = Calculation::getInstance($spreadsheet);
        $calculation->setInstanceArrayReturnType(
            Calculation::RETURN_ARRAY_AS_ARRAY
        );
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

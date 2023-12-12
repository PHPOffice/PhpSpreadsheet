<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PHPUnit\Framework\TestCase;

class DataValidationTest extends TestCase
{
    public function testLoadXlsxDataValidation(): void
    {
        $filename = 'tests/data/Reader/XLSX/dataValidationTest.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);

        $worksheet = $spreadsheet->getActiveSheet();

        self::assertTrue($worksheet->getCell('B3')->hasDataValidation());
        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Test for load drop down lists of another sheet.
     * Pull #2150, issue #2149. Also issue #2677.
     *
     * @dataProvider providerExternalSheet
     */
    public function testDataValidationOfAnotherSheet(string $expectedB14, string $filename): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);

        $worksheet = $spreadsheet->getActiveSheet();

        // same sheet
        $validationCell = $worksheet->getCell('B5');
        self::assertTrue($validationCell->hasDataValidation());
        self::assertSame(DataValidation::TYPE_LIST, $validationCell->getDataValidation()->getType());
        self::assertSame('$A$5:$A$7', $validationCell->getDataValidation()->getFormula1());

        // another sheet
        $validationCell = $worksheet->getCell('B14');
        self::assertTrue($validationCell->hasDataValidation());
        self::assertSame(DataValidation::TYPE_LIST, $validationCell->getDataValidation()->getType());
        self::assertSame($expectedB14, $validationCell->getDataValidation()->getFormula1());
        $spreadsheet->disconnectWorksheets();
    }

    public static function providerExternalSheet(): array
    {
        return [
            'standard spreadsheet' => ['Feuil2!$A$3:$A$5', 'tests/data/Reader/XLSX/dataValidation2Test.xlsx'],
            'alternate namespace prefix' => ['Feuil2!$A$3:$A$5', 'tests/data/Reader/XLSX/issue.2677.namespace.xlsx'],
            'missing formula' => ['', 'tests/data/Reader/XLSX/issue.2677.removeformula1.xlsx'],
        ];
    }
}

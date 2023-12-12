<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PHPUnit\Framework\TestCase;

class DataValidationBooleanValue extends TestCase
{
    public static function testPr2225TrueFalse(): void
    {
        //This file is created with LibreOffice
        $xlsxFile = 'tests/data/Reader/XLSX/pr2225-datavalidation-truefalse.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($xlsxFile);

        $a1 = $spreadsheet->getActiveSheet()->getDataValidation('A1');
        $a2 = $spreadsheet->getActiveSheet()->getDataValidation('A2');

        //<dataValidation allowBlank="false" showDropDown="false" showErrorMessage="true" showInputMessage="true" sqref="A1" type="list">
        //<dataValidation allowBlank="true" showDropDown="true" showErrorMessage="false" showInputMessage="false" sqref="A2" type="list">

        self::assertFalse($a1->getAllowBlank(), 'A1 validation does not allow blanks, flag should be false');
        self::assertTrue($a1->getShowDropDown(), 'A1 is set to show the drop down in Excel, which is false in the file');
        self::assertTrue($a1->getShowErrorMessage(), 'A1 Shows error message, flag should be true');
        self::assertTrue($a1->getShowInputMessage(), 'A1 Shows input message, flag should be true');

        self::assertTrue($a2->getAllowBlank(), 'A2 validation allows blanks, flag should be true');
        self::assertFalse($a2->getShowDropDown(), 'A2 is set to not show the drop down in Excel, which is true in the file');
        self::assertFalse($a2->getShowErrorMessage(), 'A2 does not show error message, flag should be false');
        self::assertFalse($a2->getShowInputMessage(), 'A2 does not show input message, flag should be false');
    }

    //This file was created with Google Sheets export to XLSX
    public static function testPr2225OneZero(): void
    {
        $xlsxFile = 'tests/data/Reader/XLSX/pr2225-datavalidation-onezero.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($xlsxFile);

        $a1 = $spreadsheet->getActiveSheet()->getDataValidation('A1');
        $a2 = $spreadsheet->getActiveSheet()->getDataValidation('A2');

        //<dataValidation allowBlank="1" showErrorMessage="1" showInputMessage="1" sqref="A1" type="list">
        //<dataValidation allowBlank="1" showDropDown="0" sqref="A2" type="list">

        self::assertTrue($a1->getAllowBlank(), 'A1 validation allows blanks, flag should be true');
        self::assertTrue($a1->getShowDropDown(), 'A1 is set to show the drop down in Excel, which is false in the file');
        self::assertTrue($a1->getShowErrorMessage(), 'A1 Shows error message, flag should be true');
        self::assertTrue($a1->getShowInputMessage(), 'A1 Shows input message, flag should be true');

        self::assertTrue($a2->getAllowBlank(), 'A2 validation allows blanks, flag should be true');
        self::assertFalse($a2->getShowDropDown(), 'A2 is set to not show the drop down in Excel, which is true in the file');
        self::assertFalse($a2->getShowErrorMessage(), 'A2 does not show error message, flag should be false');
        self::assertFalse($a2->getShowInputMessage(), 'A2 does not show input message, flag should be false');
    }
}

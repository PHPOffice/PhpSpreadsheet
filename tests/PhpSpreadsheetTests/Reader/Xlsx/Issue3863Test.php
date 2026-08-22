<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\DataValidator;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Issue3863Test extends \PHPUnit\Framework\TestCase
{
    private static string $testbook = 'tests/data/Reader/XLSX/issue.3863.xlsx';

    public function testPreliminaries(): void
    {
        $file = 'zip://';
        $file .= self::$testbook;
        $file .= '#xl/worksheets/sheet1.xml';
        $data = file_get_contents($file);
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            // Only 1 Data Validation and it does not specify operator
            self::assertStringContainsString('<dataValidations count="1"><dataValidation type="whole" allowBlank="1" showInputMessage="1" showErrorMessage="1" sqref="A1" xr:uid="{D0F98CC5-7234-4ADF-BD42-F33321DCD3CA}"><formula1>5</formula1><formula2>10</formula2></dataValidation></dataValidations>', $data);
        }
    }

    public function testValidData(): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load(self::$testbook);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('between', $sheet->getCell('A1')->getDataValidation()->getOperator());
        $validator = new DataValidator();
        self::assertTrue($validator->isValid($sheet->getCell('A1')));
        $sheet->getCell('A1')->setValue(3);
        self::assertFalse($validator->isValid($sheet->getCell('A1')));
        $sheet->getCell('A1')->setValue(7);
        self::assertTrue($validator->isValid($sheet->getCell('A1')));
        $spreadsheet->disconnectWorksheets();
    }
}

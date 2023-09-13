<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class DataValidatorTest extends TestCase
{
    public function testNoValidation(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $testCell = $sheet->getCell('A1');

        self::assertTrue($testCell->hasValidValue(), 'a cell without any validation data is always valid');
    }

    public function testUnsupportedType(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $testCell = $sheet->getCell('A1');

        $validation = $testCell->getDataValidation();
        $validation->setType(DataValidation::TYPE_CUSTOM);
        $validation->setAllowBlank(true);

        self::assertFalse($testCell->hasValidValue(), 'cannot assert that value is valid when the validation type is not supported');
    }

    public function testList(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $testCell = $sheet->getCell('A1');

        $validation = $testCell->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);

        // blank value
        $testCell->setValue('');
        $validation->setAllowBlank(true);
        self::assertTrue($testCell->hasValidValue(), 'cell can be empty');
        $validation->setAllowBlank(false);
        self::assertFalse($testCell->hasValidValue(), 'cell can not be empty');

        // inline list
        $validation->setFormula1('"yes,no"');
        $testCell->setValue('foo');
        self::assertFalse($testCell->hasValidValue(), "cell value ('foo') is not allowed");
        $testCell->setValue('yes');
        self::assertTrue($testCell->hasValidValue(), "cell value ('yes') has to be allowed");

        // list from cells
        $sheet->getCell('B1')->setValue(5);
        $sheet->getCell('B2')->setValue(6);
        $sheet->getCell('B3')->setValue(7);
        $testCell = $sheet->getCell('A1'); // redefine $testCell, because it has broken coordinates after using other cells
        $validation->setFormula1('B1:B3');
        $testCell->setValue('10');
        self::assertFalse($testCell->hasValidValue(), "cell value ('10') is not allowed");
        $testCell = $sheet->getCell('A1'); // redefine $testCell, because it has broken coordinates after using other cells
        $testCell->setValue('5');
        self::assertTrue($testCell->hasValidValue(), "cell value ('5') has to be allowed");

        $testCell = $sheet->getCell('A1'); // redefine $testCell, because it has broken coordinates after using other cells
        $validation->setFormula1('broken : cell : coordinates');

        self::assertFalse($testCell->hasValidValue(), 'invalid formula should not throw exceptions');
    }
}

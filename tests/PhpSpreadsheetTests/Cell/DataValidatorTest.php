<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\NamedRange;
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
        $spreadsheet->disconnectWorksheets();
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
        $spreadsheet->disconnectWorksheets();
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
        $validation->setFormula1('$B$1:$B$3');
        $testCell->setValue('10');
        self::assertFalse($testCell->hasValidValue(), "cell value ('10') is not allowed");
        $testCell = $sheet->getCell('A1'); // redefine $testCell, because it has broken coordinates after using other cells
        $testCell->setValue('5');
        self::assertTrue($testCell->hasValidValue(), "cell value ('5') has to be allowed");

        $testCell = $sheet->getCell('A1'); // redefine $testCell, because it has broken coordinates after using other cells
        $validation->setFormula1('broken : cell : coordinates');

        self::assertFalse($testCell->hasValidValue(), 'invalid formula should not throw exceptions');
        $spreadsheet->disconnectWorksheets();
    }

    public function testInvalidNumeric(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $validation = $sheet->getCell('A1')->getDataValidation();
        $validation->setType(DataValidation::TYPE_WHOLE)
            ->setOperator(DataValidation::OPERATOR_EQUAL)
            ->setFormula1('broken : cell : coordinates');
        $sheet->getCell('A1')->setValue(0);
        self::assertFalse($sheet->getCell('A1')->hasValidValue(), 'invalid formula should return false');
        $validation->setOperator('invalid operator')
            ->setFormula1('0');
        self::assertFalse($sheet->getCell('A1')->hasValidValue(), 'invalid operator should return false');

        $spreadsheet->disconnectWorksheets();
    }

    public function testDefinedNameAsList(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue(1);
        $sheet->getCell('A2')->setValue(3);
        $sheet->getCell('A3')->setValue(5);
        $sheet->getCell('A4')->setValue(7);
        $spreadsheet->addNamedRange(new NamedRange('listvalues', $sheet, '$A$1:$A$4'));

        $validation = $sheet->getCell('D4')->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST)
            ->setFormula1('listvalues');
        $sheet->getCell('D4')->setValue(2);
        self::assertFalse($sheet->getCell('D4')->hasValidValue());
        $sheet->getCell('D4')->setValue(3);
        self::assertTrue($sheet->getCell('D4')->hasValidValue());

        $spreadsheet->disconnectWorksheets();
    }
}

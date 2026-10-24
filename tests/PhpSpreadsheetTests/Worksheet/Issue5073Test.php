<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class Issue5073Test extends TestCase
{
    public function testSecondRangeValidation(): void
    {
        // getDataValidation threw for a range once a range validation existed
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $validation1 = $sheet->getDataValidation('A2:A105');
        $validation1->setType(DataValidation::TYPE_LIST)
            ->setAllowBlank(false)
            ->setShowDropDown(true)
            ->setFormula1('"JC,SC,3P"');

        self::assertFalse($sheet->dataValidationExists('B2:B105'));
        $validation2 = $sheet->getDataValidation('B2:B105');
        self::assertNotSame($validation1, $validation2);
        self::assertSame('B2:B105', $validation2->getSqref());
        self::assertSame(DataValidation::TYPE_NONE, $validation2->getType());
        $validation2->setType(DataValidation::TYPE_WHOLE)
            ->setOperator(DataValidation::OPERATOR_BETWEEN)
            ->setFormula1('1')
            ->setFormula2('9');
        self::assertTrue($sheet->dataValidationExists('B2:B105'));
        self::assertSame($validation2, $sheet->getDataValidation('B2:B105'));

        self::assertSame($validation1, $sheet->getDataValidation('A2:A105'));
        self::assertSame($validation1, $sheet->getDataValidation('A50'));
        self::assertSame($validation2, $sheet->getDataValidation('B50'));
        self::assertTrue($sheet->dataValidationExists('B105'));
        self::assertFalse($sheet->dataValidationExists('B106'));

        self::assertFalse($sheet->dataValidationExists('C1:C3 D5'));
        $validation3 = $sheet->getDataValidation('C1:C3 D5');
        self::assertNotSame($validation1, $validation3);
        self::assertNotSame($validation2, $validation3);
        self::assertSame('C1:C3 D5', $validation3->getSqref());
        self::assertTrue($sheet->dataValidationExists('C1:C3 D5'));
        self::assertSame($validation3, $sheet->getDataValidation('D5'));

        self::assertSame(
            ['A2:A105', 'B2:B105', 'C1:C3 D5'],
            array_keys($sheet->getDataValidationCollection())
        );

        $sheet->getCell('A3')->setValue('SC');
        self::assertTrue($sheet->getCell('A3')->hasValidValue());
        $sheet->getCell('A4')->setValue('XX');
        self::assertFalse($sheet->getCell('A4')->hasValidValue());
        $sheet->getCell('B3')->setValue(5);
        self::assertTrue($sheet->getCell('B3')->hasValidValue());
        $sheet->getCell('B4')->setValue(10);
        self::assertFalse($sheet->getCell('B4')->hasValidValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testRangeInsideExistingRange(): void
    {
        // a range is a validation of its own, as in setDataValidation
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $validation1 = $sheet->getDataValidation('A:A');
        $validation1->setType(DataValidation::TYPE_LIST)
            ->setFormula1('"JC,SC,3P"');
        self::assertSame($validation1, $sheet->getDataValidation('A6'));

        self::assertFalse($sheet->dataValidationExists('A5:A7'));
        $validation2 = $sheet->getDataValidation('A5:A7');
        self::assertNotSame($validation1, $validation2);
        self::assertSame('A5:A7', $validation2->getSqref());
        self::assertTrue($sheet->dataValidationExists('A5:A7'));
        self::assertSame(['A:A', 'A5:A7'], array_keys($sheet->getDataValidationCollection()));
        $spreadsheet->disconnectWorksheets();
    }
}

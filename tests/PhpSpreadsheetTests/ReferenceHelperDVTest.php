<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class ReferenceHelperDVTest extends TestCase
{
    public function testInsertRowsWithDataValidation(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([['First'], ['Second'], ['Third'], ['Fourth']], null, 'A5', true);
        $cellAddress = 'E5';
        $this->setDataValidation($sheet, $cellAddress);

        $sheet->insertNewRowBefore(2, 2);

        self::assertFalse(
            $sheet->getCell($cellAddress)->hasDataValidation()
        );
        self::assertTrue($sheet->getCell('E7')->hasDataValidation());
        self::assertSame('E7', $sheet->getDataValidation('E7')->getSqref());
        self::assertSame('$A$7:$A$10', $sheet->getDataValidation('E7')->getFormula1());
        $spreadsheet->disconnectWorksheets();
    }

    public function testDeleteRowsWithDataValidation(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([['First'], ['Second'], ['Third'], ['Fourth']], null, 'A5', true);
        $cellAddress = 'E5';
        $this->setDataValidation($sheet, $cellAddress);

        $sheet->removeRow(2, 2);

        self::assertFalse(
            $sheet->getCell($cellAddress)->hasDataValidation()
        );
        self::assertTrue($sheet->getCell('E3')->hasDataValidation());
        self::assertSame('E3', $sheet->getDataValidation('E3')->getSqref());
        self::assertSame('$A$3:$A$6', $sheet->getDataValidation('E3')->getFormula1());

        $spreadsheet->disconnectWorksheets();
    }

    public function testDeleteColumnsWithDataValidation(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([['First'], ['Second'], ['Third'], ['Fourth']], null, 'A5', true);
        $cellAddress = 'E5';
        $this->setDataValidation($sheet, $cellAddress);

        $sheet->removeColumn('B', 2);

        self::assertFalse(
            $sheet->getCell($cellAddress)->hasDataValidation()
        );
        self::assertTrue($sheet->getCell('C5')->hasDataValidation());
        self::assertSame('C5', $sheet->getDataValidation('C5')->getSqref());
        self::assertSame('$A$5:$A$8', $sheet->getDataValidation('C5')->getFormula1());
        $spreadsheet->disconnectWorksheets();
    }

    public function testInsertColumnsWithDataValidation(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([['First'], ['Second'], ['Third'], ['Fourth']], null, 'A5', true);
        $cellAddress = 'E5';
        $this->setDataValidation($sheet, $cellAddress);

        $sheet->insertNewColumnBefore('C', 2);

        self::assertFalse(
            $sheet->getCell($cellAddress)->hasDataValidation()
        );
        self::assertTrue($sheet->getCell('G5')->hasDataValidation());
        self::assertSame('G5', $sheet->getDataValidation('G5')->getSqref());
        self::assertSame('$A$5:$A$8', $sheet->getDataValidation('G5')->getFormula1());
        $spreadsheet->disconnectWorksheets();
    }

    public function testInsertColumnsWithDataValidation2(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([['First'], ['Second'], ['Third'], ['Fourth']], null, 'A5', true);
        $cellAddress = 'E5';
        $this->setDataValidation($sheet, $cellAddress);

        $sheet->insertNewColumnBefore('A', 2);

        self::assertFalse(
            $sheet->getCell($cellAddress)->hasDataValidation()
        );
        self::assertTrue($sheet->getCell('G5')->hasDataValidation());
        self::assertSame('G5', $sheet->getDataValidation('G5')->getSqref());
        self::assertSame('$C$5:$C$8', $sheet->getDataValidation('G5')->getFormula1());
        $spreadsheet->disconnectWorksheets();
    }

    private function setDataValidation(Worksheet $sheet, string $cellAddress): void
    {
        $validation = $sheet->getCell($cellAddress)
            ->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(
            DataValidation::STYLE_STOP
        );
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Input error');
        $validation->setError('Value is not in list.');
        $validation->setPromptTitle('Pick from list');
        $validation->setPrompt('Please pick a value from the drop-down list.');
        $validation->setFormula1('$A$5:$A$8');
    }

    public function testMultipleRanges(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('C1')->setValue(1);
        $sheet->getCell('C2')->setValue(2);
        $sheet->getCell('C3')->setValue(3);
        $dv = $sheet->getDataValidation('A1:A4 D5 E6:E7');
        $dv->setType(DataValidation::TYPE_LIST)
            ->setShowDropDown(true)
            ->setFormula1('$C$1:$C$3')
            ->setErrorStyle(DataValidation::STYLE_STOP)
            ->setShowErrorMessage(true)
            ->setErrorTitle('Input Error')
            ->setError('Value is not a member of allowed list');
        $sheet->insertNewColumnBefore('B');
        $dvs = $sheet->getDataValidationCollection();
        self::assertCount(1, $dvs);
        $expected = 'A1:A4 E5 F6:F7';
        self::assertSame([$expected], array_keys($dvs));
        $dv = $dvs[$expected];
        self::assertSame($expected, $dv->getSqref());
        self::assertSame('$D$1:$D$3', $dv->getFormula1());
        $sheet->getCell('A3')->setValue(8);
        self::assertFalse($sheet->getCell('A3')->hasValidValue());
        $sheet->getCell('E5')->setValue(7);
        self::assertFalse($sheet->getCell('E5')->hasValidValue());
        $sheet->getCell('F6')->setValue(7);
        self::assertFalse($sheet->getCell('F6')->hasValidValue());
        $sheet->getCell('F7')->setValue(1);
        self::assertTrue($sheet->getCell('F7')->hasValidValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testWholeColumn(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $dv = new DataValidation();
        $dv->setType(DataValidation::TYPE_NONE);
        $sheet->setDataValidation('A5:A7', $dv);
        $dv = new DataValidation();
        $dv->setType(DataValidation::TYPE_LIST)
            ->setShowDropDown(true)
            ->setFormula1('"Item A,Item B,Item C"')
            ->setErrorStyle(DataValidation::STYLE_STOP)
            ->setShowErrorMessage(true)
            ->setErrorTitle('Input Error')
            ->setError('Value is not a member of allowed list');
        $sheet->setDataValidation('A:A', $dv);
        $dv = new DataValidation();
        $dv->setType(DataValidation::TYPE_NONE);
        $sheet->setDataValidation('A9', $dv);
        self::assertSame(DataValidation::TYPE_LIST, $sheet->getDataValidation('A4')->getType());
        self::assertSame(DataValidation::TYPE_LIST, $sheet->getDataValidation('A10')->getType());
        self::assertSame(DataValidation::TYPE_NONE, $sheet->getDataValidation('A6')->getType());
        self::assertSame(DataValidation::TYPE_NONE, $sheet->getDataValidation('A9')->getType());
        $spreadsheet->disconnectWorksheets();
    }

    public function testWholeRow(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $dv = new DataValidation();
        $dv->setType(DataValidation::TYPE_NONE);
        $sheet->setDataValidation('C1:F1', $dv);
        $dv = new DataValidation();
        $dv->setType(DataValidation::TYPE_LIST)
            ->setShowDropDown(true)
            ->setFormula1('"Item A,Item B,Item C"')
            ->setErrorStyle(DataValidation::STYLE_STOP)
            ->setShowErrorMessage(true)
            ->setErrorTitle('Input Error')
            ->setError('Value is not a member of allowed list');
        $sheet->setDataValidation('1:1', $dv);
        $dv = new DataValidation();
        $dv->setType(DataValidation::TYPE_NONE);
        $sheet->setDataValidation('H1', $dv);
        self::assertSame(DataValidation::TYPE_LIST, $sheet->getDataValidation('B1')->getType());
        self::assertSame(DataValidation::TYPE_LIST, $sheet->getDataValidation('J1')->getType());
        self::assertSame(DataValidation::TYPE_NONE, $sheet->getDataValidation('D1')->getType());
        self::assertSame(DataValidation::TYPE_NONE, $sheet->getDataValidation('H1')->getType());
        $spreadsheet->disconnectWorksheets();
    }

    public function testFormula2(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue(5);
        $sheet->getCell('A5')->setValue(9);
        $dv = new DataValidation();
        $dv->setType(DataValidation::TYPE_WHOLE)
            ->setOperator(DataValidation::OPERATOR_BETWEEN)
            ->setFormula1('$A$1')
            ->setFormula2('$A$5')
            ->setErrorStyle(DataValidation::STYLE_STOP)
            ->setShowErrorMessage(true)
            ->setErrorTitle('Input Error')
            ->setError('Value is not whole number within bounds');
        $sheet->setDataValidation('B2', $dv);
        $sheet->insertNewRowBefore(2);
        $dv2 = $sheet->getCell('B3')->getDataValidation();
        self::assertSame('$A$1', $dv2->getFormula1());
        self::assertSame('$A$6', $dv2->getFormula2());

        $sheet->getCell('B3')->setValue(7);
        self::assertTrue($sheet->getCell('B3')->hasValidValue());
        $sheet->getCell('B3')->setValue(1);
        self::assertFalse($sheet->getCell('B3')->hasValidValue());
        $spreadsheet->disconnectWorksheets();
    }
}

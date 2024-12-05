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
}

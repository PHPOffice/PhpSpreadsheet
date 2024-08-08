<?php

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class CellArrayFormulaTest extends TestCase
{
    private bool $skipUpdateInSpillageRange = true;

    public function testSetValueArrayFormulaNoSpillage(): void
    {
        $spreadsheet = new Spreadsheet();
        Calculation::getInstance($spreadsheet)->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $cell = $spreadsheet->getActiveSheet()->getCell('A1');
        $cell->setValue('=MAX(ABS({5, -3; 1, -12}))');

        self::assertSame(12, $cell->getCalculatedValue());

        $spreadsheet->disconnectWorksheets();
    }

    public function testSetValueArrayFormulaWithSpillage(): void
    {
        $spreadsheet = new Spreadsheet();
        Calculation::getInstance($spreadsheet)->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $cell = $spreadsheet->getActiveSheet()->getCell('A1');
        $cell->setValue('=SEQUENCE(3, 3, 1, 1)');

        self::assertSame([[1, 2, 3], [4, 5, 6], [7, 8, 9]], $cell->getCalculatedValue());

        $spreadsheet->disconnectWorksheets();
    }

    public function testSetValueInSpillageRangeCell(): void
    {
        $spreadsheet = new Spreadsheet();
        Calculation::getInstance($spreadsheet)->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $sheet = $spreadsheet->getActiveSheet();
        $cell = $sheet->getCell('A1');
        $cell->setValue('=SEQUENCE(3, 3, 1, 1)');

        $cellAddress = 'C3';
        $sheet->getCell($cellAddress)->setValue('x');

        self::assertSame('#SPILL!', $sheet->getCell('A1')->getCalculatedValue());
        self::assertSame('x', $sheet->getCell('C3')->getCalculatedValue());

        $spreadsheet->disconnectWorksheets();
    }

    public function testUpdateValueInSpillageRangeCell(): void
    {
        $spreadsheet = new Spreadsheet();
        Calculation::getInstance($spreadsheet)->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue('=SEQUENCE(3, 3, 1, 1)');
        $sheet->getCell('A1')->getCalculatedValue();
        $attributes = $sheet->getCell('A1')->getFormulaAttributes();
        if (!isset($attributes, $attributes['ref'])) {
            self::fail('No formula attributes for cell A1');
        }
        $cellRange = $attributes['ref'];
        $cellAddress = 'C3';
        self::assertTrue($sheet->getCell($cellAddress)->isInRange($cellRange));
        if ($this->skipUpdateInSpillageRange) {
            $spreadsheet->disconnectWorksheets();
            self::markTestIncomplete('Preventing update in spill range not yet implemented');
        }

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Cell {$cellAddress} is within the spillage range of a formula, and cannot be changed");
        $sheet->getCell($cellAddress)->setValue('PHP');

        $spreadsheet->disconnectWorksheets();
    }

    public function testUpdateArrayFormulaForSpillageRange(): void
    {
        $spreadsheet = new Spreadsheet();
        $calculation = Calculation::getInstance($spreadsheet);
        $calculation->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue('=SEQUENCE(3, 3, 1, 1)');
        $sheet->getCell('A1')->getCalculatedValue();
        self::assertSame([[1, 2, 3], [4, 5, 6], [7, 8, 9]], $sheet->toArray(formatData: false, reduceArrays: true));

        $sheet->getCell('A1')->setValue('=SEQUENCE(2, 2, 4, -1)');
        $calculation->clearCalculationCache();
        $sheet->getCell('A1')->getCalculatedValue();
        self::assertSame([[4, 3, null], [2, 1, null], [null, null, null]], $sheet->toArray(formatData: false, reduceArrays: true));

        $cellAddress = 'C3';
        $sheet->getCell($cellAddress)->setValue('PHP');
        self::assertSame('PHP', $sheet->getCell($cellAddress)->getValue(), 'change cell formerly in spill range');

        $spreadsheet->disconnectWorksheets();
    }
}

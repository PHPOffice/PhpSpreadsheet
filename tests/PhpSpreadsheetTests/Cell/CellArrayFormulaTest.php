<?php

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class CellArrayFormulaTest extends TestCase
{
    public function testSetValueArrayFormulaNoSpillage(): void
    {
        $spreadsheet = new Spreadsheet();
        $cell = $spreadsheet->getActiveSheet()->getCell('A1');
        $cell->setValueExplicit('=MAX(ABS({5, -3; 1, -12}))', DataType::TYPE_FORMULA, true);

        self::assertSame(12, $cell->getCalculatedValue());
        self::assertTrue($cell->isArrayFormula());
        self::assertSame('A1', $cell->arrayFormulaRange());

        $spreadsheet->disconnectWorksheets();
    }

    public function testSetValueArrayFormulaWithSpillage(): void
    {
        $spreadsheet = new Spreadsheet();
        $cell = $spreadsheet->getActiveSheet()->getCell('A1');
        $cell->setValueExplicit('=SEQUENCE(3, 3, 1, 1)', DataType::TYPE_FORMULA, true, 'A1:C3');

        self::assertSame(1, $cell->getCalculatedValue());
        self::assertTrue($cell->isArrayFormula());
        self::assertSame('A1:C3', $cell->arrayFormulaRange());

        $spreadsheet->disconnectWorksheets();
    }

    public function testSetValueInSpillageRangeCell(): void
    {
        $spreadsheet = new Spreadsheet();
        $cell = $spreadsheet->getActiveSheet()->getCell('A1');
        $cell->setValueExplicit('=SEQUENCE(3, 3, 1, 1)', DataType::TYPE_FORMULA, true, 'A1:C3');

        $cellAddress = 'C3';
        $spillageCell = $spreadsheet->getActiveSheet()->getCell($cellAddress);
        self::assertTrue($spillageCell->isInSpillageRange());

        $spreadsheet->disconnectWorksheets();
    }

    public function testUpdateValueInSpillageRangeCell(): void
    {
        $spreadsheet = new Spreadsheet();
        $cell = $spreadsheet->getActiveSheet()->getCell('A1');
        $cell->setValueExplicit('=SEQUENCE(3, 3, 1, 1)', DataType::TYPE_FORMULA, true, 'A1:C3');

        $cellAddress = 'C3';
        $spillageCell = $spreadsheet->getActiveSheet()->getCell($cellAddress);
        self::assertTrue($spillageCell->isInSpillageRange());

        self::expectException(Exception::class);
        self::expectExceptionMessage("Cell {$cellAddress} is within the spillage range of a formula, and cannot be changed");
        $spillageCell->setValue('PHP');

        $spreadsheet->disconnectWorksheets();
    }

    public function testUpdateArrayFormulaForSpillageRange(): void
    {
        $spreadsheet = new Spreadsheet();
        $cell = $spreadsheet->getActiveSheet()->getCell('A1');
        $cell->setValueExplicit('=SEQUENCE(3, 3, 1, 1)', DataType::TYPE_FORMULA, true, 'A1:C3');

        $cell->setValueExplicit('=SEQUENCE(2, 2, 4, -1)', DataType::TYPE_FORMULA, true, 'A1:B2');

        self::assertSame(4, $cell->getCalculatedValue());
        self::assertTrue($cell->isArrayFormula());
        self::assertSame('A1:B2', $cell->arrayFormulaRange());

        $spreadsheet->disconnectWorksheets();
    }

    public function testSetValueToFormerSpillageCell(): void
    {
        $spreadsheet = new Spreadsheet();
        $cell = $spreadsheet->getActiveSheet()->getCell('A1');
        $cell->setValueExplicit('=SEQUENCE(3, 3, 1, 1)', DataType::TYPE_FORMULA, true, 'A1:C3');

        $cell->setValueExplicit('=SEQUENCE(2, 2, 4, -1)', DataType::TYPE_FORMULA, true, 'A1:B2');

        $cellAddress = 'C3';
        $formerSpillageCell = $spreadsheet->getActiveSheet()->getCell($cellAddress);
        $formerSpillageCell->setValue('PHP');

        self::assertSame('PHP', $formerSpillageCell->getValue());

        $spreadsheet->disconnectWorksheets();
    }
}

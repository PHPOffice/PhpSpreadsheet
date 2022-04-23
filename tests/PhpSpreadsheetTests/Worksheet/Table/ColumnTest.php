<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet\Table;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\Column;

class ColumnTest extends SetupTeardown
{
    protected function initTable(): Table
    {
        $sheet = $this->getSheet();
        $sheet->getCell('G1')->setValue('Heading');
        $sheet->getCell('G2')->setValue(2);
        $sheet->getCell('G3')->setValue(3);
        $sheet->getCell('G4')->setValue(4);
        $sheet->getCell('H1')->setValue('Heading2');
        $sheet->getCell('H2')->setValue(1);
        $sheet->getCell('H3')->setValue(2);
        $sheet->getCell('H4')->setValue(3);
        $this->maxRow = $maxRow = 4;
        $table = new Table();
        $table->setRange("G1:H$maxRow");

        return $table;
    }

    public function testVariousGets(): void
    {
        $table = $this->initTable();
        $column = $table->getColumn('H');
        $result = $column->getColumnIndex();
        self::assertEquals('H', $result);
    }

    public function testGetBadColumnIndex(): void
    {
        $this->expectException(PhpSpreadsheetException::class);
        $this->expectExceptionMessage('Column is outside of current table range.');
        $table = $this->initTable();
        $table->getColumn('B');
    }

    public function testSetColumnIndex(): void
    {
        $table = $this->initTable();
        $column = $table->getColumn('H');
        $column->setShowFilterButton(false);
        $expectedResult = 'G';

        $result = $column->setColumnIndex($expectedResult);
        self::assertInstanceOf(Column::class, $result);

        $result = $result->getColumnIndex();
        self::assertEquals($expectedResult, $result);
    }

    public function testVariousSets(): void
    {
        $table = $this->initTable();
        $column = $table->getColumn('H');

        $result = $column->setShowFilterButton(false);
        self::assertInstanceOf(Column::class, $result);
        self::assertFalse($column->getShowFilterButton());

        $label = 'Total';
        $result = $column->setTotalsRowLabel($label);
        self::assertInstanceOf(Column::class, $result);
        self::assertEquals($label, $column->getTotalsRowLabel());

        $function = 'sum';
        $result = $column->setTotalsRowFunction($function);
        self::assertInstanceOf(Column::class, $result);
        self::assertEquals($function, $column->getTotalsRowFunction());

        $formula = '=SUM(Sales_Data[[#This Row],[Q1]:[Q4]])';
        $result = $column->setColumnFormula($formula);
        self::assertInstanceOf(Column::class, $result);
        self::assertEquals($formula, $column->getColumnFormula());
    }

    public function testTable(): void
    {
        $table = $this->initTable();
        $column = new Column('H');
        $column->setTable($table);
        self::assertEquals($table, $column->getTable());
    }
}

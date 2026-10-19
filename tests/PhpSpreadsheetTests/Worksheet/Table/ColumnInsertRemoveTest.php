<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet\Table;

use PhpOffice\PhpSpreadsheet\Worksheet\Table;

class ColumnInsertRemoveTest extends SetupTeardown
{
    private function createTable(): Table
    {
        $sheet = $this->getSheet();
        $sheet->fromArray([
            ['Name', 'Qty', 'Price'],
            ['a', 1, 2],
            ['b', 3, 4],
            ['c', 5, 6],
        ]);
        $sheet->setCellValue('F1', '=SUM(Sales[Price])');
        $table = new Table('A1:C4', 'Sales');
        $sheet->addTable($table);

        return $table;
    }

    public function testRemoveColumnBeforeTable(): void
    {
        $sheet = $this->getSheet();
        $sheet->setCellValue('A10', 'x');
        $sheet->fromArray([
            ['Name', 'Qty', 'Price'],
            ['a', 1, 2],
        ], null, 'B1');
        $table = new Table('B1:D2', 'Sales');
        $sheet->addTable($table);

        $sheet->removeColumn('A');

        self::assertSame('A1:C2', $table->getRange());
        self::assertSame(['Name', 'Qty', 'Price'], $sheet->rangeToArray('A1:C1')[0]);
    }

    public function testRemoveFirstColumnOfTable(): void
    {
        $sheet = $this->getSheet();
        $table = $this->createTable();

        $sheet->removeColumn('A');

        self::assertSame('A1:B4', $table->getRange());
        self::assertSame(['Qty', 'Price'], $sheet->rangeToArray('A1:B1')[0]);
        self::assertSame('=SUM(Sales[Price])', $sheet->getCell('E1')->getValue());
    }

    public function testInsertColumnInTable(): void
    {
        $sheet = $this->getSheet();
        $table = $this->createTable();

        $sheet->insertNewColumnBefore('B');

        self::assertSame('A1:D4', $table->getRange());
        self::assertSame('Qty', $sheet->getCell('C1')->getValue());
        self::assertSame('Price', $sheet->getCell('D1')->getValue());
        self::assertSame('=SUM(Sales[Price])', $sheet->getCell('G1')->getValue());
    }
}

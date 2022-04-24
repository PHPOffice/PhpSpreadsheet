<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet\Table;

use PhpOffice\PhpSpreadsheet\Worksheet\Table;

class RemoveTableTest extends SetupTeardown
{
    private const INITIAL_RANGE = 'H2:O256';

    public function testRemoveTable(): void
    {
        $sheet = $this->getSheet();

        $table = new Table(self::INITIAL_RANGE);
        $table->setName('Table1');
        $sheet->addTable($table);

        self::assertEquals(1, $sheet->getTableCollection()->count());

        $sheet->removeTableByName('table1'); // case insensitive
        self::assertEquals(0, $sheet->getTableCollection()->count());
    }

    public function testRemoveCollection(): void
    {
        $sheet = $this->getSheet();

        $table = new Table(self::INITIAL_RANGE);
        $table->setName('Table1');
        $sheet->addTable($table);

        self::assertEquals(1, $sheet->getTableCollection()->count());

        $sheet->removeTableCollection();
        self::assertEquals(0, $sheet->getTableCollection()->count());
    }
}

<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet\Table;

use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;

class TableStyleTest extends SetupTeardown
{
    private const INITIAL_RANGE = 'H2:O256';

    public function testVariousSets(): void
    {
        $table = new Table(self::INITIAL_RANGE);
        $style = $table->getStyle();

        $result = $style->setTheme(TableStyle::TABLE_STYLE_DARK1);
        self::assertInstanceOf(TableStyle::class, $result);
        self::assertEquals(TableStyle::TABLE_STYLE_DARK1, $style->getTheme());

        $result = $style->setShowFirstColumn(true);
        self::assertInstanceOf(TableStyle::class, $result);
        self::assertTrue($style->getShowFirstColumn());

        $result = $style->setShowLastColumn(true);
        self::assertInstanceOf(TableStyle::class, $result);
        self::assertTrue($style->getShowLastColumn());

        $result = $style->setShowRowStripes(true);
        self::assertInstanceOf(TableStyle::class, $result);
        self::assertTrue($style->getShowRowStripes());

        $result = $style->setShowColumnStripes(true);
        self::assertInstanceOf(TableStyle::class, $result);
        self::assertTrue($style->getShowColumnStripes());
    }

    public function testTable(): void
    {
        $table = new Table(self::INITIAL_RANGE);
        $style = new TableStyle();
        $style->setTable($table);
        self::assertEquals($table, $style->getTable());
    }
}

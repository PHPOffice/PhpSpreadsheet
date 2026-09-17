<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx\Streaming;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Streaming\StreamedCell;
use PHPUnit\Framework\TestCase;

class StreamedCellTest extends TestCase
{
    public function testHoldsValueStyleAndType(): void
    {
        $cell = new StreamedCell('=A1', 3, DataType::TYPE_STRING);
        self::assertSame('=A1', $cell->value);
        self::assertSame(3, $cell->styleId);
        self::assertSame(DataType::TYPE_STRING, $cell->dataType);
    }

    public function testDefaults(): void
    {
        $cell = new StreamedCell(1.5);
        self::assertSame(1.5, $cell->value);
        self::assertNull($cell->styleId);
        self::assertNull($cell->dataType);
    }
}

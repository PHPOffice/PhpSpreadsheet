<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xml;

use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\Reader\Xml as XmlReader;
use PHPUnit\Framework\TestCase;

class RowDimensionTest extends TestCase
{
    public function testRowDimension(): void
    {
        $file = 'tests/data/Reader/Xml/x5j6.dontuse';
        $reader = new XmlReader();
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame(3, $sheet->getHighestRow());
        self::assertSame('test1', $sheet->getCell('A1')->getValue());
        self::assertSame('test3', $sheet->getCell('A3')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testColDimension(): void
    {
        $this->expectException(SpreadsheetException::class);
        $this->expectExceptionMessage('Invalid cell coordinate');
        $file = 'tests/data/Reader/Xml/x5j6.b.dontuse';
        $reader = new XmlReader();
        $reader->load($file);
    }
}

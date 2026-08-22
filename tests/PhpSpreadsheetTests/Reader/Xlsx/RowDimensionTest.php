<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PHPUnit\Framework\TestCase;

class RowDimensionTest extends TestCase
{
    public function testRowDimension(): void
    {
        $file = 'tests/data/Reader/XLSX/2x6m.dontuse';
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame(1, $sheet->getHighestRow());
        self::assertSame(1, $sheet->getCell('A1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }
}

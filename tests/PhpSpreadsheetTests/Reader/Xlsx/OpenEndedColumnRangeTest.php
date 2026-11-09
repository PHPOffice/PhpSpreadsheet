<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PHPUnit\Framework\TestCase;

class OpenEndedColumnRangeTest extends TestCase
{
    // Data in A1:C3; the sheet has <col min="4" max="16384" width="14.42578125" style="1"/>,
    // which is what Excel writes when a style is applied to whole columns up to the end of the sheet.
    private const INFILE = 'tests/data/Reader/XLSX/open.ended.col.range.xlsx';

    public function testHighestColumn(): void
    {
        $reader = new XlsxReader();
        $spreadsheet = $reader->load(self::INFILE);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('C', $sheet->getHighestDataColumn());
        self::assertSame('D', $sheet->getHighestColumn());
        self::assertSame('A1:D3', $sheet->calculateWorksheetDimension());
        self::assertCount(1, $sheet->getColumnDimensions());
        $spreadsheet->disconnectWorksheets();
    }

    public function testFirstColumnOfRangeKeepsAttributes(): void
    {
        $reader = new XlsxReader();
        $spreadsheet = $reader->load(self::INFILE);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertTrue($sheet->columnDimensionExists('D'));
        self::assertEqualsWithDelta(14.42578125, $sheet->getColumnDimension('D')->getWidth(), 1e-8);
        self::assertSame(1, $sheet->getColumnDimension('D')->getXfIndex());
        $spreadsheet->disconnectWorksheets();
    }

    public function testToArrayStaysWithinUsedColumns(): void
    {
        $reader = new XlsxReader();
        $spreadsheet = $reader->load(self::INFILE);
        $sheet = $spreadsheet->getActiveSheet();
        $array = $sheet->toArray();
        self::assertCount(3, $array);
        self::assertCount(4, $array[0]);
        self::assertSame(['Code', 'Qty', 'Price', null], $array[0]);
        $spreadsheet->disconnectWorksheets();
    }
}

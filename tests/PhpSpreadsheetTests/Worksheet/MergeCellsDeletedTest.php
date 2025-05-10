<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PHPUnit\Framework\TestCase;

class MergeCellsDeletedTest extends TestCase
{
    public function testDeletedColumns(): void
    {
        $infile = 'tests/data/Reader/XLSX/issue.282.xlsx';
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($infile);
        $sheet = $spreadsheet->getSheetByNameOrThrow('Sheet1');

        $mergeCells = $sheet->getMergeCells();
        self::assertSame(['B1:F1', 'G1:I1'], array_values($mergeCells));

        // Want to delete column B,C,D,E,F
        $sheet->removeColumnByIndex(2, 5);
        $mergeCells2 = $sheet->getMergeCells();
        self::assertSame(['B1:D1'], array_values($mergeCells2));
        $spreadsheet->disconnectWorksheets();
    }

    public function testDeletedRows(): void
    {
        $infile = 'tests/data/Reader/XLSX/issue.282.xlsx';
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($infile);
        $sheet = $spreadsheet->getSheetByNameOrThrow('Sheet2');

        $mergeCells = $sheet->getMergeCells();
        self::assertSame(['A2:A6', 'A7:A9'], array_values($mergeCells));

        // Want to delete rows 2 to 4
        $sheet->removeRow(2, 3);
        $mergeCells2 = $sheet->getMergeCells();
        self::assertSame(['A4:A6'], array_values($mergeCells2));
        $spreadsheet->disconnectWorksheets();
    }
}

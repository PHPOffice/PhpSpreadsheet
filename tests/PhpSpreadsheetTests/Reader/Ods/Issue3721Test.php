<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PHPUnit\Framework\TestCase;

class Issue3721Test extends TestCase
{
    public function testIssue2810ReadEmpty(): void
    {
        // Problems with getHighestDataColumn
        $filename = 'tests/data/Reader/Ods/issue.3721.ods';
        $reader = new Ods();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getSheetByNameOrThrow('sheet with data');
        $origHigh = $sheet->getHighestDataColumn();
        self::assertSame('C', $origHigh);
        $cells = [];
        foreach ($sheet->getRowIterator() as $row) {
            foreach ($row->getCellIterator(iterateOnlyExistingCells: true) as $cell) {
                $cells[] = $cell->getCoordinate();
            }
        }
        self::assertSame(['A1', 'B1', 'C1', 'A2', 'B2', 'C2'], $cells);
        self::assertSame('C', $sheet->getHighestDataColumn());
        self::assertSame('BL', $sheet->getHighestColumn());
        $spreadsheet->disconnectWorksheets();
    }

    public function testIssue2810NoReadEmpty(): void
    {
        // Problems with getHighestDataColumn
        $filename = 'tests/data/Reader/Ods/issue.3721.ods';
        $reader = new Ods();
        $reader->setReadEmptyCells(false);
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getSheetByNameOrThrow('sheet with data');
        $origHigh = $sheet->getHighestDataColumn();
        self::assertSame('C', $origHigh);
        $cells = [];
        foreach ($sheet->getRowIterator() as $row) {
            foreach ($row->getCellIterator(iterateOnlyExistingCells: true) as $cell) {
                $cells[] = $cell->getCoordinate();
            }
        }
        self::assertSame(['A1', 'B1', 'C1', 'A2', 'B2', 'C2'], $cells);
        self::assertSame('C', $sheet->getHighestDataColumn());
        self::assertSame('C', $sheet->getHighestColumn());
        $spreadsheet->disconnectWorksheets();
    }
}

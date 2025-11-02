<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class ProtectedRangeTest extends TestCase
{
    public function testInsertRow1(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->protectCells('B2:D4 J2:L4 F2:H4', name: 'ProtectedBlock1');
        $sheet->protectCells('M2:O4 Q7:R9 T1:T3');
        $sheet->protectCells('B8 C9 D1', name: 'ProtectedBlock3');
        $sheet->insertNewRowBefore(2);
        $ranges = $sheet->getProtectedCellRanges();
        $rangeKeys = array_keys($ranges);
        self::assertSame(
            [
                'B3:D5 J3:L5 F3:H5',
                'M3:O5 Q8:R10 T1:T4',
                'B9 C10 D1',
            ],
            $rangeKeys
        );
        self::assertSame('ProtectedBlock1', $ranges[$rangeKeys[0]]->getName());
        self::assertSame('ProtectedBlock3', $ranges[$rangeKeys[2]]->getName());
        $spreadsheet->disconnectWorksheets();
    }

    public function testRemoveRow1(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->protectCells('B2:D4 J2:L4 F2:H4');
        $sheet->protectCells('M2:O4 Q7:R9 T1:T3', name: 'ProtectedBlock2');
        $sheet->protectCells('B8 C9 D1 E3');
        $sheet->removeRow(3);
        $ranges = $sheet->getProtectedCellRanges();
        $rangeKeys = array_keys($ranges);
        // PhpSpreadsheet has methods to merge cell addresses in a row,
        // but not in a column. So the results here are not as concise as
        // they might be, but they are nevertheless accurate.
        self::assertSame(
            [
                'B2:B3 C2:C3 D2:D3 F2:F3 G2:G3 H2:H3 J2:J3 K2:K3 L2:L3',
                'M2:M3 N2:N3 O2:O3 Q6:Q8 R6:R8 T1:T2',
                'B7 C8 D1',
            ],
            $rangeKeys
        );
        self::assertSame('ProtectedBlock2', $ranges[$rangeKeys[1]]->getName());
        $spreadsheet->disconnectWorksheets();
    }
}

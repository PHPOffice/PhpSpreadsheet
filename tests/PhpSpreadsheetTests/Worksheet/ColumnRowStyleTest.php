<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class ColumnRowStyleTest extends TestCase
{
    public function testColumnStyle(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $columnStyle = $sheet->getStyle('B:C');
        $columnStyle->applyFromArray([
            'font' => ['name' => 'Who knows'],
        ]);
        self::assertSame(
            'Who knows',
            $sheet->getColumnStyle('B')?->getFont()->getName()
        );
        self::assertSame(
            'Who knows',
            $sheet->getColumnStyle('C')?->getFont()->getName()
        );
        self::assertNull(
            $sheet->getColumnStyle('A')?->getFont()->getName()
        );
        self::assertNull(
            $sheet->getColumnStyle('D')?->getFont()->getName()
        );

        $spreadsheet->disconnectWorksheets();
    }

    public function testRowStyle(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $rowStyle = $sheet->getStyle('2:3');
        $rowStyle->applyFromArray([
            'font' => ['name' => 'Who knows'],
        ]);
        self::assertSame(
            'Who knows',
            $sheet->getRowStyle(2)?->getFont()->getName()
        );
        self::assertSame(
            'Who knows',
            $sheet->getRowStyle(3)?->getFont()->getName()
        );
        self::assertNull(
            $sheet->getRowStyle(1)?->getFont()->getName()
        );
        self::assertNull(
            $sheet->getRowStyle(4)?->getFont()->getName()
        );

        $spreadsheet->disconnectWorksheets();
    }
}

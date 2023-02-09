<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Cell\CellAddress;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class PageBreakTest extends TestCase
{
    public function testBreaksString(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setBreak('A20', Worksheet::BREAK_ROW);
        $sheet->setBreak('A40', Worksheet::BREAK_ROW);
        $sheet->setBreak('H1', Worksheet::BREAK_COLUMN);
        $sheet->setBreak('X1', Worksheet::BREAK_COLUMN);
        $breaks1 = $sheet->getBreaks();
        self::assertSame(
            [
                'A20' => Worksheet::BREAK_ROW,
                'A40' => Worksheet::BREAK_ROW,
                'H1' => Worksheet::BREAK_COLUMN,
                'X1' => Worksheet::BREAK_COLUMN,
            ],
            $breaks1
        );
        $sheet->setBreak('A40', Worksheet::BREAK_NONE);
        $sheet->setBreak('H1', Worksheet::BREAK_NONE);
        $sheet->setBreak('XX1', Worksheet::BREAK_NONE);
        $breaks2 = $sheet->getBreaks();
        self::assertSame(
            [
                'A20' => Worksheet::BREAK_ROW,
                'X1' => Worksheet::BREAK_COLUMN,
            ],
            $breaks2
        );
        $spreadsheet->disconnectWorksheets();
    }

    public function testBreaksArray(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setBreak([1, 20], Worksheet::BREAK_ROW);
        $sheet->setBreak([1, 40], Worksheet::BREAK_ROW);
        $sheet->setBreak([8, 1], Worksheet::BREAK_COLUMN);
        $sheet->setBreak([24, 1], Worksheet::BREAK_COLUMN);
        $breaks1 = $sheet->getBreaks();
        self::assertSame(
            [
                'A20' => Worksheet::BREAK_ROW,
                'A40' => Worksheet::BREAK_ROW,
                'H1' => Worksheet::BREAK_COLUMN,
                'X1' => Worksheet::BREAK_COLUMN,
            ],
            $breaks1
        );
        $sheet->setBreak([1, 40], Worksheet::BREAK_NONE);
        $sheet->setBreak([8, 1], Worksheet::BREAK_NONE);
        $sheet->setBreak([50, 1], Worksheet::BREAK_NONE);
        $breaks2 = $sheet->getBreaks();
        self::assertSame(
            [
                'A20' => Worksheet::BREAK_ROW,
                'X1' => Worksheet::BREAK_COLUMN,
            ],
            $breaks2
        );
        $spreadsheet->disconnectWorksheets();
    }

    public function testBreaksCellAddress(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setBreak(new CellAddress('A20'), Worksheet::BREAK_ROW, 16383);
        $sheet->setBreak(new CellAddress('A40', $sheet), Worksheet::BREAK_ROW);
        $sheet->setBreak(new CellAddress('H1'), Worksheet::BREAK_COLUMN);
        $sheet->setBreak(new CellAddress('X1', $sheet), Worksheet::BREAK_COLUMN);
        $breaks1 = $sheet->getBreaks();
        self::assertSame(
            [
                'A20' => Worksheet::BREAK_ROW,
                'A40' => Worksheet::BREAK_ROW,
                'H1' => Worksheet::BREAK_COLUMN,
                'X1' => Worksheet::BREAK_COLUMN,
            ],
            $breaks1
        );
        $sheet->setBreak(new CellAddress('A40'), Worksheet::BREAK_NONE);
        $sheet->setBreak(new CellAddress('H1', $sheet), Worksheet::BREAK_NONE);
        $sheet->setBreak(new CellAddress('XX1'), Worksheet::BREAK_NONE);
        $breaks2 = $sheet->getBreaks();
        self::assertSame(
            [
                'A20' => Worksheet::BREAK_ROW,
                'X1' => Worksheet::BREAK_COLUMN,
            ],
            $breaks2
        );
        $spreadsheet->disconnectWorksheets();
    }

    public function testBreaksOtherMethods(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setBreak('A20', Worksheet::BREAK_ROW, 16383);
        $sheet->setBreak('A40', Worksheet::BREAK_ROW);
        $sheet->setBreak('H1', Worksheet::BREAK_COLUMN);
        $sheet->setBreak('X1', Worksheet::BREAK_COLUMN);

        $rowBreaks = $sheet->getRowBreaks();
        self::assertCount(2, $rowBreaks);
        self::assertSame(Worksheet::BREAK_ROW, $rowBreaks['A20']->getBreakType());
        self::assertSame('A20', $rowBreaks['A20']->getCoordinate());
        self::assertSame(16383, $rowBreaks['A20']->getMaxColOrRow());
        self::assertSame(1, $rowBreaks['A20']->getColumnInt());
        self::assertSame('A', $rowBreaks['A20']->getColumnString());
        self::assertSame(20, $rowBreaks['A20']->getRow());
        self::assertSame(Worksheet::BREAK_ROW, $rowBreaks['A20']->getBreakType());
        self::assertSame('A40', $rowBreaks['A40']->getCoordinate());
        self::assertSame(-1, $rowBreaks['A40']->getMaxColOrRow());
        self::assertSame(1, $rowBreaks['A40']->getColumnInt());
        self::assertSame('A', $rowBreaks['A40']->getColumnString());
        self::assertSame(40, $rowBreaks['A40']->getRow());
        self::assertSame(Worksheet::BREAK_ROW, $rowBreaks['A40']->getBreakType());

        $columnBreaks = $sheet->getColumnBreaks();
        self::assertCount(2, $columnBreaks);
        self::assertSame(Worksheet::BREAK_COLUMN, $columnBreaks['H1']->getBreakType());
        self::assertSame('H1', $columnBreaks['H1']->getCoordinate());
        self::assertSame(8, $columnBreaks['H1']->getColumnInt());
        self::assertSame('H', $columnBreaks['H1']->getColumnString());
        self::assertSame(1, $columnBreaks['H1']->getRow());
        self::assertSame(Worksheet::BREAK_COLUMN, $columnBreaks['H1']->getBreakType());
        self::assertSame('X1', $columnBreaks['X1']->getCoordinate());
        self::assertSame(24, $columnBreaks['X1']->getColumnInt());
        self::assertSame('X', $columnBreaks['X1']->getColumnString());
        self::assertSame(1, $columnBreaks['X1']->getRow());
        self::assertSame(Worksheet::BREAK_COLUMN, $columnBreaks['X1']->getBreakType());
        $spreadsheet->disconnectWorksheets();
    }
}

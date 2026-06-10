<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\AddressRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class CursorTest extends TestCase
{
    public function testCursor(): void
    {
        $data = [
            ['1a', '1b', '1c', '1d', '1e'],
            ['2a', '2b', '2c', '2d', '2e'],
            ['3a', '3b', '3c', '3d', '3e'],
            ['4a', '4b', '4c', '4d', '4e'],
            ['5a', '5b', '5c', '5d', '5e'],
        ];
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($data);
        $cell = $sheet->getCell('A1')->cursorRight();
        self::assertSame('1b', $cell->getValue());
        $cell = $cell->cursorDown(2)->cursorLeft();
        self::assertSame('3a', $cell->getValue());
        $cell = $cell->cursorUp()->cursorRight(3);
        self::assertSame('2d', $cell->getValue());
        $cell = $cell->cursorUp(2);
        self::assertSame('1d', $cell->getValue(), 'no move above row 1');
        $cell = $cell->cursorLeft(5);
        self::assertSame('1a', $cell->getValue(), 'no move to the left of column A');
        $cell = $cell->cursorRight(AddressRange::MAX_COLUMN_INT);
        self::assertSame(AddressRange::MAX_COLUMN . '1', $cell->getCoordinate(), 'no column beyond MAX_COLUMN_INT');
        $cell = $cell->cursorDown(AddressRange::MAX_ROW);
        self::assertSame(AddressRange::MAX_COLUMN . AddressRange::MAX_ROW, $cell->getCoordinate(), 'no row beyond MAX_ROW');
        self::assertSame(
            '4d',
            $sheet->getCell('A2')
                ->cursorDown(2) // takes us to A4
                ->cursorRight(3) // takes us to D4
                ->getValue()
        );
        $sheet->getCell('H5')->setValue(15)
            ->cursorDown()->setValue(16)
            ->cursorDown()->setValue(17);
        self::assertSame(15, $sheet->getCell('H5')->getValue());
        self::assertSame(16, $sheet->getCell('H6')->getValue());
        self::assertSame(17, $sheet->getCell('H7')->getValue());

        $cell = $sheet->getCell('H5')->cursorXlsLimits();
        self::assertSame('H5', $cell->getCoordinate());
        $cell = $cell->cursorRow(2);
        self::assertSame('H2', $cell->getCoordinate());
        $cell = $cell->cursorRow(0);
        self::assertSame('H1', $cell->getCoordinate());
        $cell = $cell->cursorColumn('ABC');
        self::assertSame('ABC1', $cell->getCoordinate());

        $cell = $sheet->getCell('JK71234')->cursorXlsLimits();
        self::assertSame('IV65536', $cell->getCoordinate());

        $spreadsheet->disconnectWorksheets();
    }
}

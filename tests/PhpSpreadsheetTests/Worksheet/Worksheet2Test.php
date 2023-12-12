<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class Worksheet2Test extends TestCase
{
    public function testMiscellaneous(): void
    {
        $invalid = Worksheet::getInvalidCharacters();
        self::assertSame(['*', ':', '/', '\\', '?', '[', ']'], $invalid);
        $worksheet = new Worksheet();
        self::assertEmpty($worksheet->getStyles());
        $worksheet->disconnectCells();
        self::assertSame([], $worksheet->getCoordinates());
    }

    public function testHighestColumn(): void
    {
        $worksheet = new Worksheet();
        $worksheet->getCell('A1')->setValue(1);
        $worksheet->getCell('B1')->setValue(2);
        $worksheet->getCell('A2')->setValue(3);
        self::assertSame('B', $worksheet->getHighestColumn(1));
        self::assertSame('A', $worksheet->getHighestColumn(2));
    }

    public function testHighestRow(): void
    {
        $worksheet = new Worksheet();
        $worksheet->getCell('A1')->setValue(1);
        $worksheet->getCell('B1')->setValue(2);
        $worksheet->getCell('B2')->setValue(3);
        self::assertSame(1, $worksheet->getHighestRow('A'));
        self::assertSame(2, $worksheet->getHighestRow('B'));
        self::assertSame(['row' => 2, 'column' => 'B'], $worksheet->getHighestRowAndColumn());
    }

    public function testUnmergeNonRange(): void
    {
        $this->expectException(SpreadsheetException::class);
        $this->expectExceptionMessage('Merge can only be removed from a range');
        $worksheet = new Worksheet();
        $worksheet->unmergeCells('A1');
    }

    public function testUnprotectNotProtected(): void
    {
        $this->expectException(SpreadsheetException::class);
        $this->expectExceptionMessage('Cell range A1:B2 not known as protected');
        $worksheet = new Worksheet();
        $worksheet->unprotectCells('A1:B2');
    }

    public function testFreezeRange(): void
    {
        $this->expectException(SpreadsheetException::class);
        $this->expectExceptionMessage('Freeze pane can not be set on a range');
        $worksheet = new Worksheet();
        $worksheet->freezePane('A1:B2');
    }

    private function getPane(Worksheet $sheet): ?string
    {
        return $sheet->getFreezePane();
    }

    public function testFreeze(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $pane = $worksheet->getActivePane();
        self::assertEmpty($pane);
        $worksheet->setSelectedCells('D3');
        $worksheet->freezePane('A2');
        $freeze = $this->getPane($worksheet);
        $pane = $worksheet->getActivePane();
        $selected = $worksheet->getSelectedCells();
        self::assertSame('A2', $freeze);
        self::assertSame('D3', $selected);
        self::assertSame('bottomLeft', $pane);
        $worksheet->unfreezePane();
        self::assertNull($this->getPane($worksheet));
        $freeze = $this->getPane($worksheet);
        $pane = $worksheet->getActivePane();
        $selected = $worksheet->getSelectedCells();
        self::assertEmpty($freeze);
        self::assertEquals('', $pane);
        self::assertSame('D3', $selected);
        $spreadsheet->disconnectWorksheets();
    }

    public function testFreezeA1(): void
    {
        $worksheet = new Worksheet();
        $worksheet->freezePane('A1');
        $freeze = $this->getPane($worksheet);
        self::assertNull($freeze);
    }

    public function testInsertBeforeRowOne(): void
    {
        $this->expectException(SpreadsheetException::class);
        $this->expectExceptionMessage('Rows can only be inserted before at least row 1');
        $worksheet = new Worksheet();
        $worksheet->insertNewRowBefore(0);
    }

    public function testRemoveBeforeRowOne(): void
    {
        $this->expectException(SpreadsheetException::class);
        $this->expectExceptionMessage('Rows to be deleted should at least start from row 1');
        $worksheet = new Worksheet();
        $worksheet->removeRow(0);
    }

    public function testInsertNumericColumn(): void
    {
        $this->expectException(SpreadsheetException::class);
        $this->expectExceptionMessage('Column references should not be numeric');
        $worksheet = new Worksheet();
        $worksheet->insertNewColumnBefore('0');
    }

    public function testRemoveNumericColumn(): void
    {
        $this->expectException(SpreadsheetException::class);
        $this->expectExceptionMessage('Column references should not be numeric');
        $worksheet = new Worksheet();
        $worksheet->removeColumn('0');
    }

    public function testInsertColumnByIndexBeforeOne(): void
    {
        $this->expectException(SpreadsheetException::class);
        $this->expectExceptionMessage('Columns can only be inserted before at least column A (1)');
        $worksheet = new Worksheet();
        $worksheet->insertNewColumnBeforeByIndex(0);
    }

    public function testRemoveColumnByIndexBeforeOne(): void
    {
        $this->expectException(SpreadsheetException::class);
        $this->expectExceptionMessage('Columns to be deleted should at least start from column A (1)');
        $worksheet = new Worksheet();
        $worksheet->removeColumnByIndex(0);
    }

    public function testInsertColumnByIndex(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->getCell('A1')->setValue(10);
        $worksheet->insertNewColumnBeforeByIndex(1);
        self::assertSame(10, $worksheet->getCell('B1')->getValue());
        self::assertNull($worksheet->getCell('A1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testRemoveColumnByIndex(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->getCell('B1')->setValue(10);
        $worksheet->removeColumnByIndex(1);
        self::assertSame(10, $worksheet->getCell('A1')->getValue());
        self::assertNull($worksheet->getCell('B1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testRemoveCommentInvalid1(): void
    {
        $this->expectException(SpreadsheetException::class);
        $this->expectExceptionMessage('Cell coordinate string can not be a range');
        $worksheet = new Worksheet();
        $worksheet->removeComment('A1:B2');
    }

    public function testRemoveCommentInvalid2(): void
    {
        $this->expectException(SpreadsheetException::class);
        $this->expectExceptionMessage('Cell coordinate string must not be absolute');
        $worksheet = new Worksheet();
        $worksheet->removeComment('$A$1');
    }

    public function testRemoveCommentInvalid3(): void
    {
        $this->expectException(SpreadsheetException::class);
        $this->expectExceptionMessage('Cell coordinate can not be zero-length string');
        $worksheet = new Worksheet();
        $worksheet->removeComment('');
    }

    public function testGetCommentInvalid1(): void
    {
        $this->expectException(SpreadsheetException::class);
        $this->expectExceptionMessage('Cell coordinate string can not be a range');
        $worksheet = new Worksheet();
        $worksheet->getComment('A1:B2');
    }

    public function testGetCommentInvalid2(): void
    {
        $this->expectException(SpreadsheetException::class);
        $this->expectExceptionMessage('Cell coordinate string must not be absolute');
        $worksheet = new Worksheet();
        $worksheet->getComment('$A$1');
    }

    public function testGetCommentInvalid3(): void
    {
        $this->expectException(SpreadsheetException::class);
        $this->expectExceptionMessage('Cell coordinate can not be zero-length string');
        $worksheet = new Worksheet();
        $worksheet->getComment('');
    }

    public function testResetTabColor(): void
    {
        $worksheet = new Worksheet();
        self::assertSame('FF000000', $worksheet->getTabColor()->getArgb());
        $worksheet->getTabColor()->setArgb('FF800000');
        self::assertSame('FF800000', $worksheet->getTabColor()->getArgb());
        $worksheet->resetTabColor();
        self::assertSame('FF000000', $worksheet->getTabColor()->getArgb());
    }
}

<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Comment;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class ByColumnAndRowTest extends TestCase
{
    public function testSetCellValueByColumnAndRow(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValueByColumnAndRow(2, 2, 2);
        self::assertSame(2, $sheet->getCell('B2')->getValue());
    }

    public function testSetCellValueExplicitByColumnAndRow(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValueExplicitByColumnAndRow(2, 2, '="PHP Rules"', DataType::TYPE_STRING);
        self::assertSame('="PHP Rules"', $sheet->getCell('B2')->getValue());
        self::assertSame(DataType::TYPE_STRING, $sheet->getCell('B2')->getDataType());
    }

    public function testCellExistsByColumnAndRow(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $cellExists = $sheet->cellExistsByColumnAndRow(2, 2);
        self::assertFalse($cellExists);

        $sheet->setCellValue('B2', 2);

        $cellExists = $sheet->cellExistsByColumnAndRow(2, 2);
        self::assertTrue($cellExists);
    }

    public function testGetCellByColumnAndRow(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('B2', 2);
        $cell = $sheet->getCellByColumnAndRow(2, 2);
        self::assertSame('B2', $cell->getCoordinate());
        self::assertSame(2, $cell->getValue());
    }

    public function testGetStyleByColumnAndRow(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $data = [['A', 'B'], ['C', 'D']];
        $sheet->fromArray($data, null, 'B2', true);
        $sheet->getStyle('B2:C3')->getFont()->setBold(true);

        $rangeStyle = $sheet->getStyleByColumnAndRow(2, 2, 3, 3);
        self::assertTrue($rangeStyle->getFont()->getBold());

        $cellStyle = $sheet->getStyleByColumnAndRow(2, 2);
        self::assertTrue($cellStyle->getFont()->getBold());
    }

    public function testSetBreakByColumnAndRow(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('B2', 2);
        $sheet->setBreakByColumnAndRow(2, 2, Worksheet::BREAK_COLUMN);

        $breaks = $sheet->getBreaks();
        self::assertArrayHasKey('B2', $breaks);
        self::assertSame(Worksheet::BREAK_COLUMN, $breaks['B2']);
    }

    public function testMergeCellsByColumnAndRow(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $data = [['A', 'B'], ['C', 'D']];
        $sheet->fromArray($data, null, 'B2', true);

        $sheet->mergeCellsByColumnAndRow(2, 2, 3, 3);
        $mergeRanges = $sheet->getMergeCells();
        self::assertArrayHasKey('B2:C3', $mergeRanges);
    }

    public function testUnergeCellsByColumnAndRow(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $data = [['A', 'B'], ['C', 'D']];
        $sheet->fromArray($data, null, 'B2', true);

        $sheet->mergeCells('B2:C3');
        $mergeRanges = $sheet->getMergeCells();
        self::assertArrayHasKey('B2:C3', $mergeRanges);

        $sheet->unmergeCellsByColumnAndRow(2, 2, 3, 3);
        $mergeRanges = $sheet->getMergeCells();
        self::assertEmpty($mergeRanges);
    }

    public function testProtectCellsByColumnAndRow(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $data = [['A', 'B'], ['C', 'D']];
        $sheet->fromArray($data, null, 'B2', true);

        $sheet->protectCellsByColumnAndRow(2, 2, 3, 3, 'secret', false);
        $protectedRanges = $sheet->getProtectedCells();
        self::assertArrayHasKey('B2:C3', $protectedRanges);
    }

    public function testUnprotectCellsByColumnAndRow(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $data = [['A', 'B'], ['C', 'D']];
        $sheet->fromArray($data, null, 'B2', true);

        $sheet->protectCells('B2:C3', 'secret', false);
        $protectedRanges = $sheet->getProtectedCells();
        self::assertArrayHasKey('B2:C3', $protectedRanges);

        $sheet->unprotectCellsByColumnAndRow(2, 2, 3, 3);
        $protectedRanges = $sheet->getProtectedCells();
        self::assertEmpty($protectedRanges);
    }

    public function testSetAutoFilterByColumnAndRow(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $data = [['A', 'B'], ['C', 'D']];
        $sheet->fromArray($data, null, 'B2', true);

        $sheet->setAutoFilterByColumnAndRow(2, 2, 3, 3);
        $autoFilter = $sheet->getAutoFilter();
        self::assertInstanceOf(AutoFilter::class, $autoFilter);
        self::assertSame('B2:C3', $autoFilter->getRange());
    }

    public function testFreezePaneByColumnAndRow(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $data = [['A', 'B'], ['C', 'D']];
        $sheet->fromArray($data, null, 'B2', true);

        $sheet->freezePaneByColumnAndRow(2, 2);
        $freezePane = $sheet->getFreezePane();
        self::assertSame('B2', $freezePane);
    }

    public function testGetCommentByColumnAndRow(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('B2', 2);
        $spreadsheet->getActiveSheet()
            ->getComment('B2')
            ->getText()->createTextRun('My Test Comment');

        $comment = $sheet->getCommentByColumnAndRow(2, 2);
        self::assertInstanceOf(Comment::class, $comment);
        self::assertSame('My Test Comment', $comment->getText()->getPlainText());
    }
}

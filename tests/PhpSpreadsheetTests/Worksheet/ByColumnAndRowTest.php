<?php

declare(strict_types=1);

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

        $sheet->setCellValue([2, 2], 2);
        self::assertSame(2, $sheet->getCell('B2')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testSetCellValueExplicitByColumnAndRow(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValueExplicit([2, 2], '="PHP Rules"', DataType::TYPE_STRING);
        self::assertSame('="PHP Rules"', $sheet->getCell('B2')->getValue());
        self::assertSame(DataType::TYPE_STRING, $sheet->getCell('B2')->getDataType());
        $spreadsheet->disconnectWorksheets();
    }

    public function testCellExistsByColumnAndRow(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $cellExists = $sheet->cellExists([2, 2]);
        self::assertFalse($cellExists);

        $sheet->setCellValue('B2', 2);

        $cellExists = $sheet->cellExists([2, 2]);
        self::assertTrue($cellExists);
        $spreadsheet->disconnectWorksheets();
    }

    public function testGetCellByColumnAndRow(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('B2', 2);
        $cell = $sheet->getCell([2, 2]);
        self::assertSame('B2', $cell->getCoordinate());
        self::assertSame(2, $cell->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testGetStyleByColumnAndRow(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $data = [['A', 'B'], ['C', 'D']];
        $sheet->fromArray($data, null, 'B2', true);
        $sheet->getStyle('B2:C3')->getFont()->setBold(true);

        $rangeStyle = $sheet->getStyle([2, 2, 3, 3]);
        self::assertTrue($rangeStyle->getFont()->getBold());

        $cellStyle = $sheet->getStyle([2, 2]);
        self::assertTrue($cellStyle->getFont()->getBold());
        $spreadsheet->disconnectWorksheets();
    }

    public function testSetBreakByColumnAndRow(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('B2', 2);
        $sheet->setBreak([2, 2], Worksheet::BREAK_COLUMN);

        $breaks = $sheet->getBreaks();
        self::assertArrayHasKey('B2', $breaks);
        self::assertSame(Worksheet::BREAK_COLUMN, $breaks['B2']);
        $spreadsheet->disconnectWorksheets();
    }

    public function testMergeCellsByColumnAndRow(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $data = [['A', 'B'], ['C', 'D']];
        $sheet->fromArray($data, null, 'B2', true);

        $sheet->mergeCells([2, 2, 3, 3]);
        $mergeRanges = $sheet->getMergeCells();
        self::assertArrayHasKey('B2:C3', $mergeRanges);
        $spreadsheet->disconnectWorksheets();
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

        $sheet->unmergeCells([2, 2, 3, 3]);
        $mergeRanges = $sheet->getMergeCells();
        self::assertEmpty($mergeRanges);
        $spreadsheet->disconnectWorksheets();
    }

    public function testProtectCellsByColumnAndRow(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $data = [['A', 'B'], ['C', 'D']];
        $sheet->fromArray($data, null, 'B2', true);

        $sheet->protectCells([2, 2, 3, 3], 'secret', false);
        $protectedRanges = $sheet->/** @scrutinizer ignore-deprecated*/ getProtectedCells();
        self::assertArrayHasKey('B2:C3', $protectedRanges);
        $protectedRanges2 = $sheet->getProtectedCellRanges();
        self::assertArrayHasKey('B2:C3', $protectedRanges2);
        $spreadsheet->disconnectWorksheets();
    }

    public function testUnprotectCellsByColumnAndRow(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $data = [['A', 'B'], ['C', 'D']];
        $sheet->fromArray($data, null, 'B2', true);

        $sheet->protectCells('B2:C3', 'secret', false);
        $protectedRanges = $sheet->getProtectedCellRanges();
        self::assertArrayHasKey('B2:C3', $protectedRanges);

        $sheet->unprotectCells([2, 2, 3, 3]);
        $protectedRanges = $sheet->getProtectedCellRanges();
        self::assertEmpty($protectedRanges);
        $spreadsheet->disconnectWorksheets();
    }

    public function testSetAutoFilterByColumnAndRow(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $data = [['A', 'B'], ['C', 'D']];
        $sheet->fromArray($data, null, 'B2', true);

        $sheet->setAutoFilter([2, 2, 3, 3]);
        $autoFilter = $sheet->getAutoFilter();
        self::assertInstanceOf(AutoFilter::class, $autoFilter);
        self::assertSame('B2:C3', $autoFilter->getRange());
        $spreadsheet->disconnectWorksheets();
    }

    public function testFreezePaneByColumnAndRow(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $data = [['A', 'B'], ['C', 'D']];
        $sheet->fromArray($data, null, 'B2', true);

        $sheet->freezePane([2, 2]);
        $freezePane = $sheet->getFreezePane();
        self::assertSame('B2', $freezePane);
        $spreadsheet->disconnectWorksheets();
    }

    public function testGetCommentByColumnAndRow(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('B2', 2);
        $spreadsheet->getActiveSheet()
            ->getComment('B2')
            ->getText()->createTextRun('My Test Comment');

        $comment = $sheet->getComment([2, 2]);
        self::assertInstanceOf(Comment::class, $comment);
        self::assertSame('My Test Comment', $comment->getText()->getPlainText());
        $spreadsheet->disconnectWorksheets();
    }
}

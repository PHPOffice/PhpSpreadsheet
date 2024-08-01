<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Comment;
use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class ByColumnAndRowUndeprecatedTest extends TestCase
{
    private ?Spreadsheet $spreadsheet = null;

    protected function tearDown(): void
    {
        if ($this->spreadsheet !== null) {
            $this->spreadsheet->disconnectWorksheets();
            $this->spreadsheet = null;
        }
    }

    private function getSpreadsheet(): Spreadsheet
    {
        $this->spreadsheet = new Spreadsheet();

        return $this->spreadsheet;
    }

    public function testSetCellValueByColumnAndRow(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue([2, 2], 2);
        self::assertSame(2, $sheet->getCell('B2')->getValue());
    }

    public function testSetCellValueExplicitByColumnAndRow(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValueExplicit([2, 2], '="PHP Rules"', DataType::TYPE_STRING);
        self::assertSame('="PHP Rules"', $sheet->getCell('B2')->getValue());
        self::assertSame(DataType::TYPE_STRING, $sheet->getCell('B2')->getDataType());
    }

    public function testCellExistsByColumnAndRow(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $cellExists = $sheet->cellExists([2, 2]);
        self::assertFalse($cellExists);

        $sheet->setCellValue('B2', 2);

        $cellExists = $sheet->cellExists([2, 2]);
        self::assertTrue($cellExists);
    }

    public function testGetCellByColumnAndRow(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('B2', 2);
        $cell = $sheet->getCell([2, 2]);
        self::assertSame('B2', $cell->getCoordinate());
        self::assertSame(2, $cell->getValue());
    }

    public function testGetStyleByColumnAndRow(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $data = [['A', 'B'], ['C', 'D']];
        $sheet->fromArray($data, null, 'B2', true);
        $sheet->getStyle('B2:C3')->getFont()->setBold(true);

        $rangeStyle = $sheet->getStyle([2, 2, 3, 3]);
        self::assertTrue($rangeStyle->getFont()->getBold());

        $cellStyle = $sheet->getStyle([2, 2]);
        self::assertTrue($cellStyle->getFont()->getBold());
    }

    public function testSetBreakByColumnAndRow(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('B2', 2);
        $sheet->setBreak([2, 2], Worksheet::BREAK_COLUMN);

        $breaks = $sheet->getBreaks();
        self::assertArrayHasKey('B2', $breaks);
        self::assertSame(Worksheet::BREAK_COLUMN, $breaks['B2']);
    }

    public function testMergeCellsByColumnAndRow(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $data = [['A', 'B'], ['C', 'D']];
        $sheet->fromArray($data, null, 'B2', true);

        $sheet->mergeCells([2, 2, 3, 3]);
        $mergeRanges = $sheet->getMergeCells();
        self::assertArrayHasKey('B2:C3', $mergeRanges);
    }

    public function testUnmergeCellsByColumnAndRow(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $data = [['A', 'B'], ['C', 'D']];
        $sheet->fromArray($data, null, 'B2', true);

        $sheet->mergeCells('B2:C3');
        $mergeRanges = $sheet->getMergeCells();
        self::assertArrayHasKey('B2:C3', $mergeRanges);

        $sheet->unmergeCells([2, 2, 3, 3]);
        $mergeRanges = $sheet->getMergeCells();
        self::assertEmpty($mergeRanges);
    }

    public function testProtectCellsByColumnAndRow(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $data = [['A', 'B'], ['C', 'D']];
        $sheet->fromArray($data, null, 'B2', true);

        $sheet->protectCells([2, 2, 3, 3], 'secret', false);
        $protectedRanges = $sheet->getProtectedCellRanges();
        self::assertArrayHasKey('B2:C3', $protectedRanges);
    }

    public function testUnprotectCellsByColumnAndRow(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $data = [['A', 'B'], ['C', 'D']];
        $sheet->fromArray($data, null, 'B2', true);

        $sheet->protectCells('B2:C3', 'secret', false);
        $protectedRanges = $sheet->getProtectedCellRanges();
        self::assertArrayHasKey('B2:C3', $protectedRanges);

        $sheet->unprotectCells([2, 2, 3, 3]);
        $protectedRanges = $sheet->getProtectedCellRanges();
        self::assertEmpty($protectedRanges);
    }

    public function testSetAutoFilterByColumnAndRow(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $data = [['A', 'B'], ['C', 'D']];
        $sheet->fromArray($data, null, 'B2', true);

        $sheet->setAutoFilter([2, 2, 3, 3]);
        $autoFilter = $sheet->getAutoFilter();
        self::assertInstanceOf(AutoFilter::class, $autoFilter);
        self::assertSame('B2:C3', $autoFilter->getRange());
    }

    public function testFreezePaneByColumnAndRow(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $data = [['A', 'B'], ['C', 'D']];
        $sheet->fromArray($data, null, 'B2', true);

        $sheet->freezePane([2, 2]);
        $freezePane = $sheet->getFreezePane();
        self::assertSame('B2', $freezePane);
    }

    public function testGetCommentByColumnAndRow(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('B2', 2);
        $spreadsheet->getActiveSheet()
            ->getComment('B2')
            ->getText()->createTextRun('My Test Comment');

        $comment = $sheet->getComment([2, 2]);
        self::assertInstanceOf(Comment::class, $comment);
        self::assertSame('My Test Comment', $comment->getText()->getPlainText());
    }

    public function testMergeCellsBadArray(): void
    {
        $this->expectException(SpreadsheetException::class);
        $this->expectExceptionMessage('CellRange array length must be 2 or 4');
        $spreadsheet = $this->getSpreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->mergeCells([2, 2, 3]);
    }
}

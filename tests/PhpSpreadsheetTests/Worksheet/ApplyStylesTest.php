<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class ApplyStylesTest extends TestCase
{
    public function testApplyFromArray(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet2 = $spreadsheet->createSheet();
        $sheet3 = $spreadsheet->createSheet();
        $cell = 'B4';
        $sheet1->getCell($cell)->setValue('first');
        $sheet1->getStyle($cell)->getFont()->setName('Arial');
        $cell = 'C9';
        $sheet2->getCell($cell)->setValue('second');
        $sheet2->getStyle($cell)->getFont()->setName('Arial');
        $cell = 'A6';
        $sheet3->getCell($cell)->setValue('third');
        $sheet3->getStyle($cell)->getFont()->setName('Arial');
        self::assertSame(2, $spreadsheet->getActiveSheetIndex());
        self::assertSame('B4', $sheet1->getSelectedCells());
        self::assertSame('C9', $sheet2->getSelectedCells());
        self::assertSame('A6', $sheet3->getSelectedCells());
        $cell = 'D12';
        $styleArray = ['font' => ['name' => 'Courier New']];
        $sheet2->getStyle($cell)->applyFromArray($styleArray);
        self::assertSame(1, $spreadsheet->getActiveSheetIndex());
        self::assertSame('B4', $sheet1->getSelectedCells());
        self::assertSame('D12', $sheet2->getSelectedCells());
        self::assertSame('A6', $sheet3->getSelectedCells());
        $spreadsheet->disconnectWorksheets();
    }

    public function testApplyStylesFromArray(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet2 = $spreadsheet->createSheet();
        $sheet3 = $spreadsheet->createSheet();
        $cell = 'B4';
        $sheet1->getCell($cell)->setValue('first');
        $sheet1->getStyle($cell)->getFont()->setName('Arial');
        $cell = 'C9';
        $sheet2->getCell($cell)->setValue('second');
        $sheet2->getStyle($cell)->getFont()->setName('Arial');
        $cell = 'A6';
        $sheet3->getCell($cell)->setValue('third');
        $sheet3->getStyle($cell)->getFont()->setName('Arial');
        self::assertSame(2, $spreadsheet->getActiveSheetIndex());
        self::assertSame('B4', $sheet1->getSelectedCells());
        self::assertSame('C9', $sheet2->getSelectedCells());
        self::assertSame('A6', $sheet3->getSelectedCells());
        $cell = 'D12';
        $styleArray = ['font' => ['name' => 'Courier New']];
        $sheet2->applyStylesFromArray($cell, $styleArray);
        self::assertSame(2, $spreadsheet->getActiveSheetIndex(), 'should be unchanged');
        self::assertSame('B4', $sheet1->getSelectedCells(), 'should be unchanged');
        self::assertSame('C9', $sheet2->getSelectedCells(), 'should be unchanged');
        self::assertSame('A6', $sheet3->getSelectedCells(), 'should be unchanged');
        $spreadsheet->disconnectWorksheets();
    }

    public function testNoSpreadsheet(): void
    {
        $sheet2 = new Worksheet();
        $cell = 'D12';
        self::assertFalse($sheet2->applyStylesFromArray($cell, ['font' => ['name' => 'Courier New']]));
    }
}

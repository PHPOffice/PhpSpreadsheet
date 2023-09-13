<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PhpOffice\PhpSpreadsheetTests\Functional;

class VisibilityTest extends Functional\AbstractFunctional
{
    public function testVisibility1(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 1);
        $sheet->setCellValue('A2', 2);
        $sheet->setCellValue('A3', 3);
        $sheet->setCellValue('B1', 4);
        $sheet->setCellValue('B2', 5);
        $sheet->setCellValue('B3', 6);
        $sheet->setCellValue('C1', 7);
        $sheet->setCellValue('C2', 8);
        $sheet->setCellValue('C3', 9);
        $sheet->getColumnDimension('B')->setVisible(false);
        $sheet->getRowDimension(2)->setVisible(false);
        $writer = new Html($spreadsheet);
        $html = $writer->generateHTMLAll();
        $reg = '/^\\s*table[.]sheet0 tr { display:none; visibility:hidden }\\s*$/m';
        $rowsrch = preg_match($reg, $html);
        self::assertEquals($rowsrch, 0);
        $reg = '/^\\s*table[.]sheet0 tr[.]row1 { display:none; visibility:hidden }\\s*$/m';
        $rowsrch = preg_match($reg, $html);
        self::assertEquals($rowsrch, 1);
        $reg = '/^\\s*table[.]sheet0 [.]column1 [{] display:none [}]\\s*$/m';
        $colsrch = preg_match($reg, $html);
        self::assertEquals($colsrch, 1);

        $this->writeAndReload($spreadsheet, 'Html');
    }

    public function testVisibility2(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 1);
        $sheet->setCellValue('A2', 2);
        $sheet->setCellValue('A3', 3);
        $sheet->setCellValue('B1', 4);
        $sheet->setCellValue('B2', 5);
        $sheet->setCellValue('B3', 6);
        $sheet->setCellValue('C1', 7);
        $sheet->setCellValue('C2', 8);
        $sheet->setCellValue('C3', 9);
        $sheet->getDefaultRowDimension()->setVisible(false);
        $sheet->getColumnDimension('B')->setVisible(false);
        $sheet->getRowDimension(1)->setVisible(true);
        $sheet->getRowDimension(3)->setVisible(true);

        $writer = new Html($spreadsheet);
        $html = $writer->generateHTMLAll();
        $reg = '/^\\s*table[.]sheet0 tr { height:15pt; display:none; visibility:hidden }\\s*$/m';
        $rowsrch = preg_match($reg, $html);
        self::assertEquals($rowsrch, 1);
        $reg = '/^\\s*table[.]sheet0 tr[.]row1 { display:none; visibility:hidden }\\s*$/m';
        $rowsrch = preg_match($reg, $html);
        self::assertEquals($rowsrch, 0);
        $reg = '/^\\s*table[.]sheet0 [.]column1 [{] display:none [}]\\s*$/m';
        $colsrch = preg_match($reg, $html);
        self::assertEquals($colsrch, 1);

        $this->writeAndReload($spreadsheet, 'Html');
    }

    public function testDefaultRowHeight(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 1);
        $sheet->getStyle('A1')->getFont()->setStrikethrough(true);
        $sheet->setCellValue('A2', 2);
        $sheet->setCellValue('A3', 3);
        $sheet->getStyle('A3')->getFont()->setStrikethrough(true)->setUnderline(Font::UNDERLINE_SINGLE);
        $sheet->setCellValue('B1', 4);
        $sheet->setCellValue('B2', 5);
        $sheet->setCellValue('B3', 6);
        $sheet->setCellValue('C1', 7);
        $sheet->setCellValue('C2', 8);
        $sheet->setCellValue('C3', 9);
        $sheet->getStyle('C3')->getFont()->setUnderline(Font::UNDERLINE_SINGLE);
        $sheet->getDefaultRowDimension()->setRowHeight(20);
        $sheet->getRowDimension(2)->setRowHeight(25);

        $writer = new Html($spreadsheet);
        $html = $writer->generateHTMLAll();
        self::assertEquals(1, substr_count($html, 'height:20pt'));
        self::assertEquals(1, substr_count($html, 'height:25pt'));
        $rowsrch = preg_match('/^\\s*table[.]sheet0 tr [{] height:20pt [}]\\s*$/m', $html);
        self::assertEquals(1, $rowsrch);
        $rowsrch = preg_match('/^\\s*table[.]sheet0 tr[.]row1 [{] height:25pt [}]\\s*$/m', $html);
        self::assertEquals(1, $rowsrch);
        $rowsrch = preg_match('/^\\s*td[.]style1, th[.]style1 [{].*text-decoration:line-through;.*[}]\\s*$/m', $html);
        self::assertEquals(1, $rowsrch);
        $rowsrch = preg_match('/^\\s*td[.]style2, th[.]style2 [{].*text-decoration:underline line-through;.*[}]\\s*$/m', $html);
        self::assertEquals(1, $rowsrch);
        $rowsrch = preg_match('/^\\s*td[.]style3, th[.]style3 [{].*text-decoration:underline;.*[}]\\s*$/m', $html);
        self::assertEquals(1, $rowsrch);

        $this->writeAndReload($spreadsheet, 'Html');
    }
}

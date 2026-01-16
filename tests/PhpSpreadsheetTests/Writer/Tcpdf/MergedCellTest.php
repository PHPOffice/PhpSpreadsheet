<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Tcpdf;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Tcpdf;
use PHPUnit\Framework\TestCase;

class MergedCellTest extends TestCase
{
    public static function testMergedCell(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $target = 'A1:A2';
        $sheet->mergeCells($target);
        $sheet->setCellValue('A1', 'Planning');
        $sheet->setSelectedCells('D1');
        $sheet->setCellValue('A4', 'Edge');
        $sheet->setShowGridlines(false);
        $writer = new Tcpdf($spreadsheet);
        $writer->setLineEnding("\n");
        $html = $writer->generateHtmlAll();
        $html = (string) preg_replace('/^\s+/m', '', $html);
        $expectedArray = [
            '<tr>',
            '<td rowspan="2" style="vertical-align:bottom; color:#000000; font-family:\'Calibri\'; font-size:11pt; text-align:left; width:42pt">Planning</td>', // row 1 has cell
            '</tr>',
            '<tr>',
            '<td>&nbsp;</td>', // row 2 with only merged cell
            '</tr>',
            '<tr>',
            '<td style="width:42pt">&nbsp;</td>', // row 3 no cell
            '</tr>',
            '<tr>',
            '<td style="vertical-align:bottom; color:#000000; font-family:\'Calibri\'; font-size:11pt; text-align:left; width:42pt">Edge</td>', // row 4 has cell
            '</tr>',
        ];
        $expectedString = implode("\n", $expectedArray);
        self::assertStringContainsString($expectedString, $html);
        $spreadsheet->disconnectWorksheets();
    }
}

<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Tcpdf;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Tcpdf as TcpdfWriter;
use PHPUnit\Framework\TestCase;

class PrintAreaTest extends TestCase
{
    public function testPrintArea(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $inArray = [
            [1, 2, 3, 4, 5],
            [6, 7, 8, 9, 10],
            [11, 12, 13, 14, 15],
            [16, 17, 18, 19, 20],
            [21, 22, 23, 24, 25],
            [26, 27, 28, 29, 30],
        ];
        $sheet->fromArray($inArray);
        $sheet->getPageSetup()->setPrintArea('B2:D4');
        $writer = new TcpdfWriter($spreadsheet);
        $html = $writer->generateHtmlAll();
        $html = preg_replace('/^ +/m', '', $html) ?? $html;
        $expectedArray = [
            '<tbody>',
            '<tr>',
            '<td></td>',
            '<td style="vertical-align:bottom; color:#000000; font-family:\'Calibri\'; font-size:11pt; text-align:right; width:42pt">7</td>',
            '<td style="vertical-align:bottom; color:#000000; font-family:\'Calibri\'; font-size:11pt; text-align:right; width:42pt">8</td>',
            '<td style="vertical-align:bottom; color:#000000; font-family:\'Calibri\'; font-size:11pt; text-align:right; width:42pt">9</td>',
            '</tr>',
            '<tr>',
            '<td></td>',
            '<td style="vertical-align:bottom; color:#000000; font-family:\'Calibri\'; font-size:11pt; text-align:right; width:42pt">12</td>',
            '<td style="vertical-align:bottom; color:#000000; font-family:\'Calibri\'; font-size:11pt; text-align:right; width:42pt">13</td>',
            '<td style="vertical-align:bottom; color:#000000; font-family:\'Calibri\'; font-size:11pt; text-align:right; width:42pt">14</td>',
            '</tr>',
            '<tr>',
            '<td></td>',
            '<td style="vertical-align:bottom; color:#000000; font-family:\'Calibri\'; font-size:11pt; text-align:right; width:42pt">17</td>',
            '<td style="vertical-align:bottom; color:#000000; font-family:\'Calibri\'; font-size:11pt; text-align:right; width:42pt">18</td>',
            '<td style="vertical-align:bottom; color:#000000; font-family:\'Calibri\'; font-size:11pt; text-align:right; width:42pt">19</td>',
            '</tr>',
            '</tbody>',
        ];
        $expectedString = implode(PHP_EOL, $expectedArray);
        self::assertStringContainsString(
            $expectedString,
            $html
        );
        $spreadsheet->disconnectWorksheets();
    }
}

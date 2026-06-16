<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Tcpdf;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Tcpdf;
use PHPUnit\Framework\TestCase;

class GridlinesInlineTest extends TestCase
{
    public function testGridlinesInline(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setShowGridlines(false);
        $sheet1->setPrintGridlines(false);
        $sheet1->fromArray([
            [11, 12, 13],
            [14, 15, 16],
        ]);
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setShowGridlines(false);
        $sheet2->setPrintGridlines(true);
        $sheet2->fromArray([
            [21, 22, 23],
            [24, 25, 26],
        ]);
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setShowGridlines(true);
        $sheet3->setPrintGridlines(false);
        $sheet3->fromArray([
            [31, 32, 33],
            [34, 35, 36],
        ]);
        $sheet4 = $spreadsheet->createSheet();
        $sheet4->setShowGridlines(true);
        $sheet4->setPrintGridlines(true);
        $sheet4->fromArray([
            [41, 42, 43],
            [44, 45, 46],
        ]);

        $writer = new Tcpdf($spreadsheet);
        $writer->writeAllSheets();
        $writer->setUseInlineCss(true);
        $html = $writer->generateHtmlAll();
        self::assertStringContainsString(
            '<td style="vertical-align:bottom; color:#000000; font-family:\'Calibri\'; font-size:11pt; text-align:right; width:42pt">11</td>',
            $html,
            'neither gridlines nor gridlinesp'
        );
        self::assertStringContainsString(
            '<td class="gridlines gridlinesp" style="vertical-align:bottom; color:#000000; font-family:\'Calibri\'; font-size:11pt; text-align:right; width:42pt; border:0.1px solid black">21</td>',
            $html,
            'gridlinesp without gridlines'
        );
        self::assertStringContainsString(
            '<td style="vertical-align:bottom; color:#000000; font-family:\'Calibri\'; font-size:11pt; text-align:right; width:42pt">31</td>',
            $html,
            'gridlines without gridlinesp'
        );
        self::assertStringContainsString(
            '<td class="gridlines gridlinesp" style="vertical-align:bottom; color:#000000; font-family:\'Calibri\'; font-size:11pt; text-align:right; width:42pt; border:0.1px solid black">41</td>',
            $html,
            'gridlines and gridlinesp'
        );
        $count = substr_count($html, 'page-break-before');
        self::assertSame(4, $count, 'repetition unlike Html/Dompdf/Mpdf');

        $spreadsheet->disconnectWorksheets();
    }
}

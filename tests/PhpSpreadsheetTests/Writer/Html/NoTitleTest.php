<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PHPUnit\Framework\TestCase;

class NoTitleTest extends TestCase
{
    public function testNoTitle(): void
    {
        $file = 'tests/data/Reader/XLSX/blankcell.xlsx';
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($file);
        self::assertSame('', $spreadsheet->getProperties()->getTitle());

        $writer = new Html($spreadsheet);
        $writer->setUseInlineCss(true);
        $html = $writer->generateHTMLAll();
        self::assertStringContainsString('<title>Sheet1</title>', $html);
        self::assertStringContainsString('<td style="vertical-align:bottom; color:#000000; font-family:\'Calibri\'; font-size:11pt; text-align:left; width:42pt" class="gridlines gridlinesp">C1</td>', $html);
        $writer->setUseInlineCss(false);
        $html = $writer->generateHTMLAll();
        self::assertStringContainsString('<td class="column2 style0 s">C1</td>', $html);
        $spreadsheet->disconnectWorksheets();
    }

    public function testHideSomeGridlines(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(
            [
                [1, 2, 3, 4, 5, 6],
                [7, 8, 9, 10, 11, 12],
                [17, 18, 19, 20, 21, 22],
                [27, 28, 29, 30, 31, 32],
                [37, 38, 39, 40, 41, 42],
            ]
        );
        $sheet->getStyle('B2:D4')->getBorders()->applyFromArray(
            [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_NONE,
                    'color' => ['rgb' => '808080'],
                ],
            ],
        );

        $writer = new Html($spreadsheet);
        $writer->setUseInlineCss(true);
        $html = $writer->generateHTMLAll();
        self::assertStringContainsString('<td style="vertical-align:bottom; color:#000000; font-family:\'Calibri\'; font-size:11pt; text-align:right; width:42pt" class="gridlines gridlinesp">7</td>', $html);
        self::assertStringContainsString('<td style="vertical-align:bottom; border-bottom:none #808080; border-top:none #808080; border-left:none #808080; border-right:none #808080; color:#000000; font-family:\'Calibri\'; font-size:11pt; text-align:right; width:42pt" class="gridlines gridlinesp">19</td>', $html);
        $spreadsheet->disconnectWorksheets();
    }
}

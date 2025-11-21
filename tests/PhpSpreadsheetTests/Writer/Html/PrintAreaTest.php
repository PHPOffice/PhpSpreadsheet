<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Reader\Html as HtmlReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
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
        $writer = new HtmlWriter($spreadsheet);
        $html = $writer->generateHtmlAll();
        self::assertStringContainsString(
            "<table border='0' cellpadding='0' cellspacing='0' data-printarea='B2:D4' id='sheet0' class='sheet0 gridlines'>",
            $html
        );
        $expectedArray = [
            '@media print {',
            '    table.sheet0 tr.row0 td { display:none }',
            '    table.sheet0 tr.row4 td { display:none }',
            '    table.sheet0 tr.row5 td { display:none }',
            '    table.sheet0 td.column0 { display:none }',
            '    table.sheet0 td.column4 { display:none }',
            '}',
        ];
        $expectedString = implode(PHP_EOL, $expectedArray);
        self::assertStringContainsString(
            $expectedString,
            $html
        );
        $spreadsheet->disconnectWorksheets();

        $reader = new HtmlReader();
        $spreadsheet2 = $reader->loadFromString($html);
        $sheet2 = $spreadsheet2->getActiveSheet();
        self::assertSame('B2:D4', $sheet2->getPageSetup()->getPrintArea());
        self::assertSame($inArray, $sheet2->toArray(null, false, false));
        $spreadsheet2->disconnectWorksheets();
    }
}

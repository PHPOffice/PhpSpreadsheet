<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use PHPUnit\Framework\TestCase;

class BadHyperlinkTest extends TestCase
{
    public function testBadHyperlink(): void
    {
        $reader = new XlsxReader();
        $infile = 'tests/data/Reader/XLSX/sec-j47r.dontuse';
        $spreadsheet = $reader->load($infile);
        $writer = new HtmlWriter($spreadsheet);
        $html = $writer->generateHtmlAll();
        self::assertStringContainsString("<td class=\"column0 style1 f\">jav\tascript:alert()</td>", $html);
        $spreadsheet->disconnectWorksheets();
    }
}

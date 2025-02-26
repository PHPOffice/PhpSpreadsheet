<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use PHPUnit\Framework\TestCase;

class BadHyperlinkBaseTest extends TestCase
{
    public function testBadHyperlinkBase(): void
    {
        $reader = new XlsxReader();
        $infile = 'tests/data/Reader/XLSX/sec-p66w.dontuse';
        $spreadsheet = $reader->load($infile);
        $writer = new HtmlWriter($spreadsheet);
        $html = $writer->generateHtmlAll();
        self::assertStringContainsString('<base href="&quot;&gt;&lt;img src=1 onerror=alert()&gt;" />', $html);
        $spreadsheet->disconnectWorksheets();
    }
}

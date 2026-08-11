<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use PHPUnit\Framework\TestCase;

class BadCustomPropertyTest extends TestCase
{
    public function testBadCustomProperty(): void
    {
        $reader = new XlsxReader();
        $infile = 'tests/data/Reader/XLSX/sec-q229.dontuse';
        $spreadsheet = $reader->load($infile);
        $writer = new HtmlWriter($spreadsheet);
        $html = $writer->generateHtmlAll();
        self::assertStringContainsString('<meta name="custom.string.custom_property&quot;&gt;&lt;img src=1 onerror=alert()&gt;" content="test" />', $html);
        $spreadsheet->disconnectWorksheets();
    }
}

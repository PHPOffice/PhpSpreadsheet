<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use PHPUnit\Framework\TestCase;

class AtSignFormatTest extends TestCase
{
    public function testAtSignFormat(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $payload = '<img src=x onerror=alert(document.domain)>';
        $formatCode = '@ "items"';
        $sheet->setCellValue('A1', $payload);
        $sheet->getStyle('A1')
            ->getNumberFormat()
            ->setFormatCode($formatCode);

        $writer = new HtmlWriter($spreadsheet);
        $html = $writer->generateHTMLAll();
        self::assertStringContainsString('<td class="column0 style1 s">&lt;img src=x onerror=alert(document.domain)&gt; items</td>', $html);
        $spreadsheet->disconnectWorksheets();
    }
}

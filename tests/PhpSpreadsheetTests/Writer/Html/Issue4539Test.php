<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PHPUnit\Framework\TestCase;

class Issue4539Test extends TestCase
{
    public function testInlineAndNot(): void
    {
        $infile = 'tests/data/Reader/XLSX/issue.4539.xlsx';
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($infile);
        $writer = new Html($spreadsheet);
        $writer->setConditionalFormatting(true);
        $writer->setUseInlineCss(true);
        $html = $writer->generateHtmlAll();
        $expected = '<td class="gridlines" style="vertical-align:bottom; color:#000000; font-family:\'Aptos Narrow\'; font-size:12pt; text-align:right; width:102pt; color:#000000;background-color:#5A8AC6;">5</td>';
        self::assertStringContainsString($expected, $html, 'inline conditional style');
        $expected = '<td class="gridlines" style="vertical-align:bottom; font-weight:bold; color:#000000; font-family:\'Aptos Narrow\'; font-size:12pt; text-align:left; width:102pt">Column Heading</td>';
        self::assertStringContainsString($expected, $html, 'inline no conditional style');

        $writer->setUseInlineCss(false);
        $html = $writer->generateHtmlAll();
        $expected = '<td class="column0 style2 n" style="color:#000000;background-color:#5A8AC6;">5</td>';
        self::assertStringContainsString($expected, $html, 'notinline conditional style');
        $expected = '<td class="column0 style1 s">Column Heading</td>';
        self::assertStringContainsString($expected, $html, 'notinline no conditional style');

        $spreadsheet->disconnectWorksheets();
    }
}

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
        $expected = '<td style="vertical-align:bottom; color:#000000; font-family:\'Aptos Narrow\'; font-size:12pt; text-align:right; width:102pt; color:#000000;background-color:#5A8AC6;">5</td>';
        self::assertStringContainsString($expected, $html, 'inline conditional style');
        $expected = '<td style="vertical-align:bottom; font-weight:bold; color:#000000; font-family:\'Aptos Narrow\'; font-size:12pt; text-align:left; width:102pt">Column Heading</td>';
        self::assertStringContainsString($expected, $html, 'inline no conditional style');
        $expected = '<td style="vertical-align:bottom; color:#000000; font-family:\'Aptos Narrow\'; font-size:12pt; text-align:right; width:102pt; border-top:1px solid #92D050 !important;color:#000000;">1</td>';
        self::assertStringContainsString($expected, $html, 'inline border-top');
        $expected = '<td style="vertical-align:bottom; color:#000000; font-family:\'Aptos Narrow\'; font-size:12pt; text-align:right; width:102pt; border-top:1px solid #FF0000 !important;font-weight:bold;color:#000000;">2</td>';
        self::assertStringContainsString($expected, $html, 'inline border-top and bold');
        $expected = '<td style="vertical-align:bottom; color:#000000; font-family:\'Aptos Narrow\'; font-size:12pt; text-align:right; width:102pt">3</td>';
        self::assertStringContainsString($expected, $html, 'inline nomatch');

        $writer->setUseInlineCss(false);
        $html = $writer->generateHtmlAll();
        $expected = '<td class="column0 style0 n" style="color:#000000;background-color:#5A8AC6;">5</td>';
        self::assertStringContainsString($expected, $html, 'notinline conditional style');
        $expected = '<td class="column0 style1 s">Column Heading</td>';
        self::assertStringContainsString($expected, $html, 'notinline no conditional style');
        $expected = '<td class="column0 style0 n" style="border-top:1px solid #92D050 !important;color:#000000;">1</td>';
        self::assertStringContainsString($expected, $html, 'notinline border-top');
        $expected = '<td class="column0 style0 n" style="border-top:1px solid #FF0000 !important;font-weight:bold;color:#000000;">2</td>';
        self::assertStringContainsString($expected, $html, 'notinline border-top bold');
        $expected = '<td class="column0 style0 n">3</td>';
        self::assertStringContainsString($expected, $html, 'notinline nomatch');

        $spreadsheet->disconnectWorksheets();
    }
}

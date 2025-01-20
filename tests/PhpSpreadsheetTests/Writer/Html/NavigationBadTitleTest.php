<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use PHPUnit\Framework\TestCase;

class NavigationBadTitleTest extends TestCase
{
    public function testNavigationTitle(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue(1);
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('<img src=x onerror=alert(1)>');
        $sheet2->getCell('A2')->setValue(2);

        $writer = new HtmlWriter($spreadsheet);
        $writer->writeAllSheets();
        $html = $writer->generateHTMLAll();
        $expected = '<ul class="navigation">'
            . PHP_EOL
            . '  <li class="sheet0"><a href="#sheet0">Worksheet</a></li>'
            . PHP_EOL
            . '  <li class="sheet1"><a href="#sheet1">&lt;img src=x onerror=alert(1)&gt;</a></li>'
            . PHP_EOL
            . '</ul>';
        self::assertStringContainsString($expected, $html, 'appropriate characters are escaped');
        $spreadsheet->disconnectWorksheets();
    }
}

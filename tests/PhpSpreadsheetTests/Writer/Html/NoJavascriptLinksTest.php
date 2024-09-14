<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PHPUnit\Framework\TestCase;

class NoJavascriptLinksTest extends TestCase
{
    public function testNoJavascriptLinks(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue('Click me');
        $hyperlink = new Hyperlink('http://www.example.com');
        $sheet->getCell('A1')->setHyperlink($hyperlink);
        $sheet->getCell('A2')->setValue('JS link');
        $hyperlink2 = new Hyperlink('javascript:alert(\'hello1\')');
        $sheet->getCell('A2')->setHyperlink($hyperlink2);
        $sheet->getCell('A3')->setValue('=HYPERLINK("javascript:alert(\'hello2\')", "jsfunc click")');

        $writer = new Html($spreadsheet);
        $html = $writer->generateHTMLAll();
        self::assertStringContainsString('<td class="column0 style0 s"><a href="http://www.example.com" title="">Click me</a></td>', $html, 'http hyperlink retained');
        self::assertStringContainsString('<td class="column0 style0 s">javascript:alert(\'hello1\')</td>', $html, 'javascript hyperlink dropped');
        self::assertStringContainsString('<td class="column0 style0 f">javascript:alert(\'hello2\')</td>', $html, 'javascript hyperlink function dropped');
        $spreadsheet->disconnectWorksheets();
    }
}

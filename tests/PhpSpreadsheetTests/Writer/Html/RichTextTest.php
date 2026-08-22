<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use PHPUnit\Framework\TestCase;

class RichTextTest extends TestCase
{
    public function testRichText(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $rtf = new RichText();
        $rtf->createText('~Cell Style~');
        $rtf->createTextRun('~RTF Style~')->getFont()?->setItalic(true);
        $rtf->createText('~No Style~');
        $sheet->getCell('A1')->setValue($rtf);
        $sheet->getStyle('A1')->getFont()->setBold(true);

        $fontStyle = $sheet->getStyle('A1')->getFont();
        self::assertTrue($fontStyle->getBold());
        self::assertFalse($fontStyle->getItalic());

        $a1Value = $sheet->getCell('A1')->getValue();
        self::assertInstanceOf(RichText::class, $a1Value);
        $elements = $a1Value->getRichTextElements();
        self::assertCount(3, $elements);
        self::assertNull($elements[0]->getFont());
        $fontStyle = $elements[1]->getFont();
        self::assertNotNull($fontStyle);
        self::assertFalse($fontStyle->getBold());
        self::assertTrue($fontStyle->getItalic());
        self::assertNull($elements[0]->getFont());

        $writer = new HtmlWriter($spreadsheet);
        $html = $writer->generateHtmlAll();
        self::assertStringContainsString('td.style1, th.style1 { vertical-align:bottom; border-bottom:none #000000; border-top:none #000000; border-left:none #000000; border-right:none #000000; font-weight:bold; color:#000000; font-family:\'Calibri\'; font-size:11pt }', $html, 'cell style');

        self::assertStringContainsString('<td class="column0 style1 inlineStr"><span style="font-weight:bold; text-decoration:normal; font-style:normal; color:#000000; font-family:\'Calibri\'; font-size:11pt">~Cell Style~</span>', $html, 'cell style and first text element');

        self::assertStringContainsString('<span style="font-weight:normal; text-decoration:normal; font-style:italic; color:#000000; font-family:\'Calibri\'; font-size:11pt">~RTF Style~</span>', $html, 'second text element');

        self::assertStringContainsString('<span style="font-weight:bold; text-decoration:normal; font-style:normal; color:#000000; font-family:\'Calibri\'; font-size:11pt">~No Style~</span></td>', $html, 'third text element');

        $spreadsheet->disconnectWorksheets();
    }

    public function testNoFont(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $rtf = new RichText();
        $rtf->createTextRun('no font')->setFont(null);
        $sheet->setCellValue('A1', $rtf);
        $writer = new HtmlWriter($spreadsheet);
        $html = $writer->generateHtmlAll();
        self::assertStringContainsString('<td class="column0 style0 inlineStr"><span>no font</span></td>', $html);
        $spreadsheet->disconnectWorksheets();
    }
}

<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use PHPUnit\Framework\TestCase;

class FloatInlineTest extends TestCase
{
    public function testFloatInline(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setPrintGridlines(false);
        $sheet1->getCell('A1')->setValue('L');
        $sheet1->getCell('B1')->setValue('T');
        $sheet1->getCell('C1')->setValue('R');

        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setShowGridlines(false);
        $sheet2->setPrintGridlines(false);
        $sheet2->setRightToLeft(true);
        $sheet2->getCell('A1')->setValue('R');
        $sheet2->getCell('B1')->setValue('T');
        $sheet2->getCell('C1')->setValue('L');

        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setShowGridlines(false);
        $sheet3->setPrintGridlines(false);
        $sheet3->getCell('A1')->setValue('l');
        $sheet3->getCell('B1')->setValue('t');
        $sheet3->getCell('C1')->setValue('r');

        $writer = new HtmlWriter($spreadsheet);
        $writer->writeAllSheets();
        $writer->setUseInlineCss(true);
        $html = $writer->generateHtmlAll();
        self::assertStringContainsString(
            "<table dir='ltr' id='sheet0' style='border-collapse:collapse; float:left' class='gridlines'>",
            $html,
            'ltr sheet in spreadsheet with both ltr and rtl sheets , with gridlines'
        );
        self::assertStringContainsString(
            "<table dir='rtl' id='sheet1' style='border-collapse:collapse; float:right'>",
            $html,
            'rtl sheet in spreadsheet with both ltr and rtl sheets, without gridlines'
        );
        self::assertStringContainsString(
            "<table dir='ltr' id='sheet2' style='border-collapse:collapse; float:left'>",
            $html,
            'second ltr sheet in spreadsheet with both ltr and rtl sheets, without gridlines'
        );

        $spreadsheet->disconnectWorksheets();
    }
}

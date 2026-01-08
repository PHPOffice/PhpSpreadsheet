<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Mpdf;

use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf as MpdfWriter;
use PHPUnit\Framework\TestCase;

class Issue4773Test extends TestCase
{
    public static function testLineBreaks(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        $sheet->getPageMargins()->setTop(0.2);
        $sheet->getPageMargins()->setRight(0.2);
        $sheet->getPageMargins()->setLeft(0.2);
        $sheet->getPageMargins()->setBottom(0.5);

        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);

        $sheet->getStyle('A1')->getFont()
            ->setSize(12)
            ->setItalic(true);
        $sheet->getStyle('A1')->getAlignment()
            ->setWrapText(true);
        $sheet->setCellValue('A1', "ABC\nDEF\nGHI");

        $richText = new RichText();
        $run1 = $richText->createTextRun("bold\n");
        $run1->getFont()?->setBold(true);
        $run3 = $richText->createTextRun('italic');
        $run3->getFont()?->setItalic(true);
        $sheet->getStyle('B1')->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);
        $sheet->setCellValue('B1', $richText);

        $writer = new MpdfWriter($spreadsheet);
        $content = $writer->generateHtmlAll();
        $expected1 = '<td class="column0 style1 s" style="width:105pt">ABC<br />DEF<br />GHI</td>';
        self::assertStringContainsString($expected1, $content, 'br tags without newline in normal text');
        $expected2 = '<td class="column1 style2 inlineStr" style="width:105pt"><span style="font-weight:bold; text-decoration:normal; font-style:normal; color:#000000; font-family:\'Calibri\'; font-size:11pt">bold<br /></span><span style="font-weight:normal; text-decoration:normal; font-style:italic; color:#000000; font-family:\'Calibri\'; font-size:11pt">italic</span></td>';
        self::assertStringContainsString($expected2, $content, 'br tag without newline in rich text');
        $spreadsheet->disconnectWorksheets();
    }
}

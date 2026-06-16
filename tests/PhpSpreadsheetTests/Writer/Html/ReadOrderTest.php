<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Reader\Html as HtmlReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use PHPUnit\Framework\TestCase;

class ReadOrderTest extends TestCase
{
    public function testInline(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', '1-' . 'منصور حسين الناصر');
        $sheet->setCellValue('A2', '1-' . 'منصور حسين الناصر');
        $sheet->setCellValue('A3', '1-' . 'منصور حسين الناصر');
        $sheet->getStyle('A1')
            ->getAlignment()->setReadOrder(Alignment::READORDER_RTL);
        $sheet->getStyle('A2')
            ->getAlignment()->setReadOrder(Alignment::READORDER_LTR);
        $sheet->getStyle('A2')->getFont()->setName('Arial');
        $sheet->getStyle('A3')->getFont()->setName('Times New Roman');
        $sheet->setCellValue('A5', 'hello');
        $sheet->getStyle('A5')->getFont()->setName('Tahoma');
        $sheet->getStyle('A5')
            ->getAlignment()->setIndent(2);
        $writer = new HtmlWriter($spreadsheet);
        $writer->setUseInlineCss(true);
        $html = $writer->generateHtmlAll();
        self::assertStringContainsString(
            '<td class="gridlines" style="vertical-align:bottom; direction:rtl; color:#000000; font-family:\'Calibri\';',
            $html
        );
        self::assertStringContainsString(
            '<td class="gridlines" style="vertical-align:bottom; direction:ltr; color:#000000; font-family:\'Arial\';',
            $html
        );
        self::assertStringContainsString(
            '<td class="gridlines" style="vertical-align:bottom; color:#000000; font-family:\'Times New Roman\';',
            $html
        );
        self::assertStringContainsString(
            '>&nbsp;</td>',
            $html
        );
        self::assertStringContainsString(
            '<td class="gridlines" style="vertical-align:bottom; text-indent:18px; color:#000000; font-family:\'Tahoma\';',
            $html
        );
        $spreadsheet->disconnectWorksheets();

        $reader = new HtmlReader();
        $spreadsheet2 = $reader->loadFromString($html);
        $sheet0 = $spreadsheet2->getActiveSheet();
        self::assertSame(
            Alignment::READORDER_RTL,
            $sheet0->getStyle('A1')->getAlignment()->getReadOrder()
        );
        self::assertSame(
            Alignment::READORDER_LTR,
            $sheet0->getStyle('A2')->getAlignment()->getReadOrder()
        );
        self::assertSame(
            Alignment::READORDER_CONTEXT,
            $sheet0->getStyle('A3')->getAlignment()->getReadOrder()
        );
        self::assertSame(
            2,
            $sheet0->getStyle('A5')->getAlignment()->getIndent()
        );
        $spreadsheet2->disconnectWorksheets();
    }

    public function testNotInline(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', '1-' . 'منصور حسين الناصر');
        $sheet->setCellValue('A2', '1-' . 'منصور حسين الناصر');
        $sheet->setCellValue('A3', '1-' . 'منصور حسين الناصر');
        $sheet->getStyle('A1')
            ->getAlignment()->setReadOrder(Alignment::READORDER_RTL);
        $sheet->getStyle('A2')
            ->getAlignment()->setReadOrder(Alignment::READORDER_LTR);
        $sheet->getStyle('A2')->getFont()->setName('Arial');
        $sheet->getStyle('A3')->getFont()->setName('Times New Roman');
        $sheet->setCellValue('A5', 'hello');
        $sheet->getStyle('A5')->getFont()->setName('Tahoma');
        $sheet->getStyle('A5')
            ->getAlignment()->setIndent(2);
        $writer = new HtmlWriter($spreadsheet);
        $html = $writer->generateHtmlAll();
        self::assertStringContainsString(
            'td.style1, th.style1 { vertical-align:bottom; direction:rtl; border-bottom',
            $html
        );
        self::assertStringContainsString(
            'td.style2, th.style2 { vertical-align:bottom; direction:ltr; border-bottom',
            $html
        );
        self::assertStringContainsString(
            'td.style3, th.style3 { vertical-align:bottom; border-bottom',
            $html
        );
        self::assertStringContainsString(
            '>&nbsp;</td>',
            $html
        );
        self::assertStringContainsString(
            'td.style4, th.style4 { vertical-align:bottom; text-indent:18px;',
            $html
        );
        $spreadsheet->disconnectWorksheets();
        // PhpSpreadsheet does not read non-inline styles
    }
}

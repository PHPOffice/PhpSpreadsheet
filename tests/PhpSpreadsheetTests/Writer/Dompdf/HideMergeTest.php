<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Dompdf;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf;
use PHPUnit\Framework\TestCase;

class HideMergeTest extends TestCase
{
    public function testHideWithMerge(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        // just some labels for better visualisation of the problem
        $worksheet->setCellValue('A1', 'A');
        $worksheet->setCellValue('B1', 'B');
        $worksheet->setCellValue('C1', 'C');
        // setting the row height to better visualize the problem
        for ($i = 1; $i <= 10; ++$i) {
            $worksheet->getRowDimension($i)->setRowHeight(17);
        }
        // Headline - merged over two cells AND two rows
        $worksheet->mergeCells('B2:C3');
        $worksheet->setCellValue('B2', 'Hello World Headline');
        $worksheet->getStyle('B2:C3')->getFont()->setBold(true);
        $worksheet->getStyle('B2:C3')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $worksheet->getStyle('B2:C3')
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->setColor(new Color(Color::COLOR_BLACK));

        // Content 1 - merge over two rows
        $worksheet->mergeCells('B4:B5');
        $worksheet->mergeCells('C4:C5');
        $worksheet->setCellValue('B4', 'Label 1');
        $worksheet->setCellValue('C4', 'Text 1');
        $worksheet->getStyle('B4:B5')->getFont()->setBold(true);
        $worksheet->getStyle('B4:C5')
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);
        $worksheet->getStyle('B4:B5')
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->setColor(new Color(Color::COLOR_BLACK));
        $worksheet->getStyle('C4:C5')
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->setColor(new Color(Color::COLOR_BLACK));

        // Content 2 - merge over two rows
        $worksheet->mergeCells('B6:B7');
        $worksheet->mergeCells('C6:C7');
        $worksheet->setCellValue('B6', 'Label 2');
        $worksheet->setCellValue('C6', 'Text 2');
        $worksheet->getStyle('B6:B7')->getFont()->setBold(true);
        $worksheet->getStyle('B6:C7')
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);
        $worksheet->getStyle('B6:B7')
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->setColor(new Color(Color::COLOR_BLACK));
        $worksheet->getStyle('C6:C7')
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->setColor(new Color(Color::COLOR_BLACK));

        // This is where the error was introduced (!!!)
        $worksheet->getColumnDimension('A')->setVisible(false);
        $Dompdf = new Dompdf($spreadsheet);
        $html = $Dompdf->generateHtmlAll();
        $html = preg_replace('/^\s+/m', '', $html) ?? $html;
        $html = preg_replace('/[\n\r]/', '', $html) ?? $html;
        self::assertStringContainsString(
            'table.sheet0 .column0 { display:none }',
            $html
        );
        self::assertStringContainsString(
            '<tr class="row0">'
                . '<td class="column0 style0 s" style="width:42pt; height:17pt">A</td>'
                . '<td class="column1 style0 s" style="width:42pt; height:17pt">B</td>'
                . '<td class="column2 style0 s" style="width:42pt; height:17pt">C</td>'
                . '</tr>',
            $html
        );
        self::assertStringContainsString(
            '<tr class="row1">'
                . '<td class="column0 style0" style="width:42pt; height:17pt">&nbsp;</td>'
                . '<td class="column1 style1 s style1" colspan="2" rowspan="2" style="width:84pt; height:17pt">Hello World Headline</td>'
                . '</tr>',
            $html
        );
        self::assertStringContainsString(
            '<tr class="row2">'
                . '<td class="column0 style0" style="width:42pt; height:17pt">&nbsp;</td>'
                . '</tr>',
            $html
        );
        self::assertStringContainsString(
            '<tr class="row3">'
                . '<td class="column0 style0" style="width:42pt; height:17pt">&nbsp;</td>'
                . '<td class="column1 style2 s style2" rowspan="2" style="width:42pt; height:17pt">Label 1</td>'
                . '<td class="column2 style3 s style3" rowspan="2" style="width:42pt; height:17pt">Text 1</td>'
                . '</tr>',
            $html
        );
        self::assertStringContainsString(
            '<tr class="row4">'
                . '<td class="column0 style0" style="width:42pt; height:17pt">&nbsp;</td>'
                . '</tr>',
            $html
        );
        self::assertStringContainsString(
            '<tr class="row5">'
                . '<td class="column0 style0" style="width:42pt; height:17pt">&nbsp;</td>'
                . '<td class="column1 style2 s style2" rowspan="2" style="width:42pt; height:17pt">Label 2</td>'
                . '<td class="column2 style3 s style3" rowspan="2" style="width:42pt; height:17pt">Text 2</td>'
                . '</tr>',
            $html
        );
        self::assertStringContainsString(
            '<tr class="row6">'
                . '<td class="column0 style0" style="width:42pt; height:17pt">&nbsp;</td>'
                . '</tr>',
            $html
        );
        $spreadsheet->disconnectWorksheets();
    }
}

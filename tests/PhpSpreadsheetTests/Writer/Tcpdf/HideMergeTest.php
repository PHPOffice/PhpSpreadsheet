<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Tcpdf;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Tcpdf;
use PHPUnit\Framework\TestCase;

class HideMergeTest extends TestCase
{
    public function testHideWithMerge(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->setPrintGridlines(true);
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
        $Tcpdf = new Tcpdf($spreadsheet);
        $html = $Tcpdf->generateHtmlAll();
        $html = preg_replace('/^\s+/m', '', $html) ?? $html;
        $html = preg_replace('/[\n\r]/', '', $html) ?? $html;
        self::assertStringContainsString(
            '<tbody><tr style="height:17pt">'
                . '<td></td>'
                . '<td class="gridlines gridlinesp" style="vertical-align:bottom; color:#000000; font-family:\'Calibri\'; font-size:11pt; text-align:left; width:42pt; height:17pt">B</td>'
                . '<td class="gridlines gridlinesp" style="vertical-align:bottom; color:#000000; font-family:\'Calibri\'; font-size:11pt; text-align:left; width:42pt; height:17pt">C</td>'
                . '</tr>',
            $html
        );
        self::assertStringContainsString(
            '<tr style="height:17pt">'
                . '<td></td>'
                . '<td class="gridlines gridlinesp" colspan="2" rowspan="2" style="vertical-align:bottom; text-align:center; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:\'Calibri\'; font-size:11pt; width:84pt; height:17pt">Hello World Headline</td>'
                . '</tr>',
            $html
        );
        $emptyRowCount = substr_count(
            $html,
            '<tr style="height:17pt">'
                . '<td></td>'
                . '</tr>'
        );
        self::assertSame(3, $emptyRowCount);
        self::assertStringContainsString(
            '<tr style="height:17pt">'
                . '<td></td>'
                . '<td class="gridlines gridlinesp" rowspan="2" style="vertical-align:middle; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:\'Calibri\'; font-size:11pt; text-align:left; width:42pt; height:17pt">Label 1</td>'
                . '<td class="gridlines gridlinesp" rowspan="2" style="vertical-align:middle; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:1px solid #000000 !important; color:#000000; font-family:\'Calibri\'; font-size:11pt; text-align:left; width:42pt; height:17pt">Text 1</td>'
                . '</tr>',
            $html
        );
        self::assertStringContainsString(
            '<tr style="height:17pt">'
                . '<td></td>'
                . '<td class="gridlines gridlinesp" rowspan="2" style="vertical-align:middle; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:\'Calibri\'; font-size:11pt; text-align:left; width:42pt; height:17pt">Label 2</td>'
                . '<td class="gridlines gridlinesp" rowspan="2" style="vertical-align:middle; border-bottom:1px solid #000000 !important; border-top:1px solid #000000 !important; border-left:1px solid #000000 !important; border-right:1px solid #000000 !important; color:#000000; font-family:\'Calibri\'; font-size:11pt; text-align:left; width:42pt; height:17pt">Text 2</td>'
                . '</tr>',
            $html
        );
        $spreadsheet->disconnectWorksheets();
    }
}

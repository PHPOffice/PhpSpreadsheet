<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Ods;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Ods;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Content;
use PHPUnit\Framework\TestCase;

class ReadOrderTest extends TestCase
{
    public function testReadOrder(): void
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
        $content = new Content(new Ods($spreadsheet));
        $xml = $content->write();
        self::assertStringContainsString(
            '<style:table-cell-properties style:vertical-align="bottom" style:rotation-align="none"/>'
                . '<style:paragraph-properties style:writing-mode="rl-tb"/>'
                . '<style:text-properties fo:color="#000000" fo:font-family="Calibri"',
            $xml,
            'explicit rtl direction in paragraph properties'
        );
        self::assertStringContainsString(
            '<style:table-cell-properties style:vertical-align="bottom" style:rotation-align="none"/>'
                . '<style:paragraph-properties style:writing-mode="lr-tb"/>'
                . '<style:text-properties fo:color="#000000" fo:font-family="Arial"',
            $xml,
            'explicit ltr direction in paragraph properties'
        );
        self::assertStringContainsString(
            '<style:table-cell-properties style:vertical-align="bottom" style:rotation-align="none"/>'
                . '<style:text-properties fo:color="#000000" fo:font-family="Times New Roman"',
            $xml,
            'no paragraph properties'
        );
        $spreadsheet->disconnectWorksheets();
    }
}

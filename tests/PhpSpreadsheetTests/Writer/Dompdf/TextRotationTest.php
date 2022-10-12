<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Dompdf;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf;
use PHPUnit\Framework\TestCase;

class TextRotationTest extends TestCase
{
    public function testTextRotation(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setPrintGridlines(true);
        $sheet->getStyle('A7')->getAlignment()->setTextRotation(90);
        $sheet->setCellValue('A7', 'Lorem Ipsum');
        $writer = new Dompdf($spreadsheet);
        $html = $writer->generateHtmlAll();
        self::assertStringContainsString(' transform:rotate(90deg);', $html);
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
    }
}

<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Mpdf;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
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
        $writer = new Mpdf($spreadsheet);
        $html = $writer->generateHtmlAll();
        self::assertStringContainsString(' text-rotate:90;', $html);
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
    }
}

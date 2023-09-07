<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Html;
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
        $writer = new Html($spreadsheet);
        $html = $writer->generateHtmlAll();
        self::assertStringContainsString(' transform:rotate(90deg);', $html);
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
    }
}

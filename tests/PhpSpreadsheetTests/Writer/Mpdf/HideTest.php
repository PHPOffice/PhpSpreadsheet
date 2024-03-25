<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Mpdf;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PHPUnit\Framework\TestCase;

class HideTest extends TestCase
{
    public function testHide(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            ['a1', 'b1', 'c1', 'd1', 'e1', 'f1'],
            ['a2', 'b2', 'c2', 'd2', 'e2', 'f2'],
            ['a3', 'b3', 'c3', 'd3', 'e3', 'f3'],
            ['a4', 'b4', 'c4', 'd4', 'e4', 'f4'],
            ['a5', 'b5', 'c5', 'd5', 'e5', 'f5'],
            ['a6', 'b6', 'c6', 'd6', 'e6', 'f6'],
        ]);
        $sheet->getColumnDimension('B')->setVisible(false);
        $sheet->getRowDimension(3)->setVisible(false);
        $writer = new Mpdf($spreadsheet);
        $html = $writer->generateHtmlAll();
        self::assertStringNotContainsString('a3', $html);
        self::assertStringNotContainsString('b1', $html);
        self::assertStringContainsString('a1', $html);
        $spreadsheet->disconnectWorksheets();
    }
}

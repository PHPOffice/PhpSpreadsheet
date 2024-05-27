<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Mpdf;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PHPUnit\Framework\TestCase;

class MergedBorderTest extends TestCase
{
    public static function testMergedBorder(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $target = 'A2:B5';
        $sheet->mergeCells($target);
        $sheet->setCellValue('A2', 'Planning');
        $sheet->getStyle($target)->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_HAIR,
                    'color' => ['rgb' => 'FF0000'],
                ],
            ],
        ]);
        $sheet->setSelectedCells('D1');
        $sheet->setCellValue('D1', 'Edge');
        $sheet->setCellValue('D5', 'Edge');
        $sheet->setShowGridlines(false);
        $writer = new Mpdf($spreadsheet);
        $html = $writer->generateHtmlAll();
        self::assertSame(0, preg_match('/border-(top|bottom|right|left):none #000000;/', $html));
        self::assertSame(1, preg_match('/border-top:1px solid #FF0000 !important; border-left:1px solid #FF0000 !important;/', $html));
        self::assertSame(1, preg_match('/border-bottom:1px solid #FF0000 !important; border-left:1px solid #FF0000 !important;/', $html));
        self::assertSame(1, preg_match('/border-top:1px solid #FF0000 !important; border-right:1px solid #FF0000 !important;/', $html));
        self::assertSame(1, preg_match('/border-bottom:1px solid #FF0000 !important; border-right:1px solid #FF0000 !important;/', $html));
        $spreadsheet->disconnectWorksheets();
    }
}

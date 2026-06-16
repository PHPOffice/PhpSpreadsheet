<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Ods;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Ods as OdsWriter;
use PHPUnit\Framework\TestCase;

class Issue4798Test extends TestCase
{
    public function testWriteRowDimensions(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 1);
        $sheet->setCellValue('A2', 1.2);
        $sheet->setCellValue('A3', -1.234);
        $sheet->setCellValue('A4', '=1+2');
        $sheet->getStyle('A1:A4')->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
        $sheet->getStyle('A1')->getFont()->setItalic(true);
        $sheet->getStyle('A2')->getFont()->setBold(true);
        $writer = new OdsWriter($spreadsheet);
        $writerWorksheet = new OdsWriter\Content($writer);
        $data = $writerWorksheet->write();
        $count = substr_count($data, 'style:data-style-name="N200"');
        self::assertSame(3, $count, 'once for the italic cell, once for the bold, and once for the other two');
        self::assertStringContainsString(
            '<number:number-style style:name="N200"><number:number number:decimal-places="2" number:min-decimal-places="2" number:min-integer-digits="1"/></number:number-style>',
            $data
        );
        $spreadsheet->disconnectWorksheets();
    }
}

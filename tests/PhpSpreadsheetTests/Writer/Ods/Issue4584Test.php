<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Ods;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Ods as OdsWriter;
use PHPUnit\Framework\TestCase;

class Issue4584Test extends TestCase
{
    public function testWriteRowDimensions(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getDefaultRowDimension()->setRowHeight(20);
        $sheet->setCellValue('A1', 'hello there world 1');
        $sheet->getStyle('A1')->getAlignment()->setWrapText(true);
        $sheet->getRowDimension(1)->setCustomFormat(true);
        $sheet->setCellValue('A2', 'hello there world 2');
        $sheet->setCellValue('A4', 'hello there world 4');
        $writer = new OdsWriter($spreadsheet);
        $writerWorksheet = new OdsWriter\Content($writer);
        $data = $writerWorksheet->write();
        self::assertStringContainsString(
            '<style:style style:family="table-row" style:name="ro0"><style:table-row-properties style:row-height="0.706cm" style:use-optimal-row-height="false" fo:break-before="auto"/></style:style>',
            $data
        );
        self::assertStringContainsString(
            '<table:table-row><table:table-cell table:style-name="ce1" office:value-type="string"><text:p>hello there world 1</text:p></table:table-cell></table:table-row>',
            $data
        );
        self::assertStringContainsString(
            '<table:table-row table:style-name="ro0"><table:table-cell table:style-name="ce0" office:value-type="string"><text:p>hello there world 2</text:p></table:table-cell></table:table-row>',
            $data
        );
        self::assertStringContainsString(
            '<table:table-row table:number-rows-repeated="1"/><table:table-row table:style-name="ro0"><table:table-cell table:style-name="ce0" office:value-type="string"><text:p>hello there world 4</text:p></table:table-cell></table:table-row>',
            $data
        );
        $spreadsheet->disconnectWorksheets();
    }
}

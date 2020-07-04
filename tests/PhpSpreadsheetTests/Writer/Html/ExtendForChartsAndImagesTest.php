<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PhpOffice\PhpSpreadsheetTests\Functional;

class ExtendForChartsAndImagesTest extends Functional\AbstractFunctional
{
    public function testEmptySheet(): void
    {
        $spreadsheet = new Spreadsheet();

        $this->assertMaxColumnAndMaxRow($spreadsheet, 1, 1);
    }

    public function testSimpleSheet(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B3', 'foo');

        $this->assertMaxColumnAndMaxRow($spreadsheet, 2, 3);
    }

    public function testSheetWithExtraColumnDimensions(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B3', 'foo');

        // Artificially expend the sheet column count without any real cells
        $sheet->getColumnDimension('E');

        $this->assertMaxColumnAndMaxRow($spreadsheet, 2, 3);
    }

    public function testSheetWithExtraRowDimensions(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B3', 'foo');

        // Artificially expend the sheet row count without any real cells
        $sheet->getRowDimension(5);

        $this->assertMaxColumnAndMaxRow($spreadsheet, 2, 3);
    }

    public function testSheetWithImageBelowData(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B3', 'foo');

        // Add a drawing to the worksheet
        $drawing = new Drawing();
        $drawing->setPath('foo.png', false);
        $drawing->setCoordinates('A5');
        $drawing->setWorksheet($sheet);

        $this->assertMaxColumnAndMaxRow($spreadsheet, 2, 5);
    }

    public function testSheetWithImageRightOfData(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B3', 'foo');

        // Add a drawing to the worksheet
        $drawing = new Drawing();
        $drawing->setPath('foo.png', false);
        $drawing->setCoordinates('E1');
        $drawing->setWorksheet($sheet);

        $this->assertMaxColumnAndMaxRow($spreadsheet, 5, 3);
    }

    private function assertMaxColumnAndMaxRow(Spreadsheet $spreadsheet, int $expectedColumnCount, int $expectedRowCount): void
    {
        $writer = new Html($spreadsheet);
        $html = $writer->generateHtmlAll();

        $rowCount = substr_count($html, '<tr ');
        self::assertSame($expectedRowCount, $rowCount);

        $columnCount = substr_count($html, '<td ') / $rowCount;

        self::assertSame($expectedColumnCount, $columnCount);
    }
}

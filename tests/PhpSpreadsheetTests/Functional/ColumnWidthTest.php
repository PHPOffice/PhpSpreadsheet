<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\Attributes\DataProvider;

class ColumnWidthTest extends AbstractFunctional
{
    public static function providerFormats(): array
    {
        return [
            ['Xlsx'],
        ];
    }

    #[DataProvider('providerFormats')]
    public function testReadColumnWidth(string $format): void
    {
        // create new sheet with column width
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Hello World !');
        $sheet->getColumnDimension('A')->setWidth(20);
        $this->assertColumn($spreadsheet);

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format);
        $this->assertColumn($reloadedSpreadsheet);
    }

    private function assertColumn(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->getActiveSheet();
        $columnDimensions = $sheet->getColumnDimensions();

        self::assertArrayHasKey('A', $columnDimensions);
        $column = array_shift($columnDimensions);
        self::assertEquals(20, $column->getWidth());
    }
}

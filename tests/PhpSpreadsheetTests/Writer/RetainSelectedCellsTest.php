<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class RetainSelectedCellsTest extends AbstractFunctional
{
    public static function providerFormats(): array
    {
        return [
            ['Xls'],
            ['Xlsx'],
            ['Ods'],
            ['Csv'],
            ['Html'],
        ];
    }

    /**
     * Test selected cell is retained in memory and in file written to disk.
     *
     * @dataProvider providerFormats
     */
    public function testRetainSelectedCells(string $format): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', '=SIN(1)')
            ->setCellValue('A2', '=SIN(2)')
            ->setCellValue('A3', '=SIN(3)')
            ->setCellValue('B1', '=SIN(4)')
            ->setCellValue('B2', '=SIN(5)')
            ->setCellValue('B3', '=SIN(6)')
            ->setCellValue('C1', '=SIN(7)')
            ->setCellValue('C2', '=SIN(8)')
            ->setCellValue('C3', '=SIN(9)');
        $sheet->setSelectedCell('A3');
        $sheet = $spreadsheet->createSheet();
        $sheet->setCellValue('A1', '=SIN(1)')
            ->setCellValue('A2', '=SIN(2)')
            ->setCellValue('A3', '=SIN(3)')
            ->setCellValue('B1', '=SIN(4)')
            ->setCellValue('B2', '=SIN(5)')
            ->setCellValue('B3', '=SIN(6)')
            ->setCellValue('C1', '=SIN(7)')
            ->setCellValue('C2', '=SIN(8)')
            ->setCellValue('C3', '=SIN(9)');
        $sheet->setSelectedCell('B1');
        $sheet = $spreadsheet->createSheet();
        $sheet->setCellValue('A1', '=SIN(1)')
            ->setCellValue('A2', '=SIN(2)')
            ->setCellValue('A3', '=SIN(3)')
            ->setCellValue('B1', '=SIN(4)')
            ->setCellValue('B2', '=SIN(5)')
            ->setCellValue('B3', '=SIN(6)')
            ->setCellValue('C1', '=SIN(7)')
            ->setCellValue('C2', '=SIN(8)')
            ->setCellValue('C3', '=SIN(9)');
        $sheet->setSelectedCell('C2');
        $spreadsheet->setActiveSheetIndex(1);

        $reloaded = $this->writeAndReload($spreadsheet, $format);
        self::assertEquals('A3', $spreadsheet->getSheet(0)->getSelectedCells());
        self::assertEquals('B1', $spreadsheet->getSheet(1)->getSelectedCells());
        self::assertEquals('C2', $spreadsheet->getSheet(2)->getSelectedCells());
        self::assertEquals(1, $spreadsheet->getActiveSheetIndex());
        // SelectedCells and ActiveSheet don't make sense for Html, Csv.
        if ($format === 'Xlsx' || $format === 'Xls' || $format === 'Ods') {
            self::assertEquals('A3', $reloaded->getSheet(0)->getSelectedCells());
            self::assertEquals('B1', $reloaded->getSheet(1)->getSelectedCells());
            self::assertEquals('C2', $reloaded->getSheet(2)->getSelectedCells());
            self::assertEquals(1, $reloaded->getActiveSheetIndex());
        }
    }
}

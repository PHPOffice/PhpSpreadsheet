<?php

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class SelectedCellsTest extends AbstractFunctional
{
    public function providerFormats(): array
    {
        return [
            ['Xls'],
        ];
    }

    /**
     * Test load file with correct selected cells.
     *
     * @dataProvider providerFormats
     *
     * @param string $format
     */
    public function testSelectedCells($format): void
    {
        $spreadsheet = new Spreadsheet();

        $spreadsheet->setActiveSheetIndex(0)
            ->setTitle('Test1')
            ->setCellValue('D1', 1)
            ->setCellValue('D2', 2)
            ->setCellValue('D3', 3)
            ->setCellValue('D4', 4)
            ->setCellValue('D5', '=SUM(D1:D4)')
            ->setSelectedCell('B2');

        $spreadsheet->createSheet(1);

        $spreadsheet->setActiveSheetIndex(1)
            ->setTitle('Test2')
            ->setCellValue('D1', 4)
            ->setCellValue('E1', 3)
            ->setCellValue('F1', 2)
            ->setCellValue('G1', 1)
            ->setCellValue('H1', '=SUM(D1:G4)')
            ->setSelectedCells('A1:B2');

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format);

        // Original object.
        self::assertSame('B2', $spreadsheet->setActiveSheetIndex(0)->getSelectedCells());
        self::assertSame('A1:B2', $spreadsheet->setActiveSheetIndex(1)->getSelectedCells());

        // Saved and reloaded file.
        self::assertSame('B2', $reloadedSpreadsheet->setActiveSheetIndex(0)->getSelectedCells());
        self::assertSame('A1:B2', $reloadedSpreadsheet->setActiveSheetIndex(1)->getSelectedCells());
    }
}

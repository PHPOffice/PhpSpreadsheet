<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class VisibilityTest extends AbstractFunctional
{
    /**
     * @dataProvider dataProviderRowVisibility
     */
    public function testRowVisibility(array $visibleRows): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        foreach ($visibleRows as $row => $visibility) {
            $worksheet->setCellValue("A{$row}", $row);
            $worksheet->getRowDimension($row)->setVisible($visibility);
        }

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $reloadedWorksheet = $reloadedSpreadsheet->getActiveSheet();
        foreach ($visibleRows as $row => $visibility) {
            self::assertSame($visibility, $reloadedWorksheet->getRowDimension($row)->getVisible());
        }
    }

    public static function dataProviderRowVisibility(): array
    {
        return [
            [
                [1 => false, 2 => false, 3 => true, 4 => false, 5 => true, 6 => false],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderColumnVisibility
     */
    public function testColumnVisibility(array $visibleColumns): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        foreach ($visibleColumns as $column => $visibility) {
            $worksheet->setCellValue("{$column}1", $column);
            $worksheet->getColumnDimension($column)->setVisible($visibility);
        }

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $reloadedWorksheet = $reloadedSpreadsheet->getActiveSheet();
        foreach ($visibleColumns as $column => $visibility) {
            self::assertSame($visibility, $reloadedWorksheet->getColumnDimension($column)->getVisible());
        }
    }

    public static function dataProviderColumnVisibility(): array
    {
        return [
            [
                ['A' => false, 'B' => false, 'C' => true, 'D' => false, 'E' => true, 'F' => false],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderSheetVisibility
     */
    public function testSheetVisibility(array $visibleSheets): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);
        foreach ($visibleSheets as $sheetName => $visibility) {
            $worksheet = $spreadsheet->addSheet(new Worksheet($spreadsheet, $sheetName));
            $worksheet->setCellValue('A1', $sheetName);
            $worksheet->setSheetState($visibility);
        }

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        foreach ($visibleSheets as $sheetName => $visibility) {
            $reloadedWorksheet = $reloadedSpreadsheet->getSheetByNameOrThrow($sheetName);
            self::assertSame($visibility, $reloadedWorksheet->getSheetState());
        }
    }

    public static function dataProviderSheetVisibility(): array
    {
        return [
            [
                [
                    'Worksheet 1' => Worksheet::SHEETSTATE_HIDDEN,
                    'Worksheet 2' => Worksheet::SHEETSTATE_VERYHIDDEN,
                    'Worksheet 3' => Worksheet::SHEETSTATE_VISIBLE,
                ],
            ],
        ];
    }
}

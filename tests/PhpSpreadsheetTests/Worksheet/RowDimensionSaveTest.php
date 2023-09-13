<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class RowDimensionSaveTest extends AbstractFunctional
{
    /**
     * @dataProvider typeProvider
     */
    public function testSaveNoAllocateRowDimension(string $type): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue(1);
        $sheet->getCell('A2')->setValue(2);
        $sheet->getCell('A3')->setValue(3);
        $sheet->getRowDimension(2)->setVisible(false);
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $type);
        self::assertFalse($sheet->rowDimensionExists(1));
        self::assertTrue($sheet->rowDimensionExists(2));
        self::assertFalse($sheet->rowDimensionExists(3));
        $spreadsheet->disconnectWorksheets();
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public static function typeProvider(): array
    {
        return [
            ['Xlsx'],
            ['Xls'],
            ['Ods'],
            ['Html'],
        ];
    }

    public function testSaveNoAllocateRowDimensionMpdf(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue(1);
        $sheet->getCell('A2')->setValue(2);
        $sheet->getCell('A3')->setValue(3);
        $sheet->getRowDimension(2)->setVisible(false);
        $writer = new Mpdf($spreadsheet);
        $writer->generateHtmlAll();
        self::assertFalse($sheet->rowDimensionExists(1));
        self::assertTrue($sheet->rowDimensionExists(2));
        self::assertFalse($sheet->rowDimensionExists(3));
        $spreadsheet->disconnectWorksheets();
    }
}

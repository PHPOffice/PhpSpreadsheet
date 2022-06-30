<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class DrawingsInsertRowsTest extends AbstractFunctional
{
    /**
     * Test save and load XLSX file with drawing on 2nd worksheet.
     */
    public function testSaveAfterInsertingRows(): void
    {
        // Read spreadsheet from file
        $inputFilename = 'tests/data/Writer/XLSX/issue.2908.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($inputFilename);
        $sheet = $spreadsheet->getActiveSheet();
        $drawingCollection = $sheet->getDrawingCollection();
        self::assertCount(1, $drawingCollection);
        $drawing = $drawingCollection[0];
        self::assertNotNull($drawing);
        self::assertSame('D10', $drawing->getCoordinates());
        self::assertSame('F11', $drawing->getCoordinates2());
        self::assertSame('oneCell', $drawing->getEditAs());

        $sheet->insertNewRowBefore(5);
        $sheet->insertNewRowBefore(6);

        // Save spreadsheet to file and read it back
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $rsheet = $reloadedSpreadsheet->getActiveSheet();
        $drawingCollection2 = $rsheet->getDrawingCollection();
        self::assertCount(1, $drawingCollection2);
        $drawing2 = $drawingCollection2[0];
        self::assertNotNull($drawing2);
        self::assertSame('D12', $drawing2->getCoordinates());
        self::assertSame('F13', $drawing2->getCoordinates2());
        self::assertSame('oneCell', $drawing2->getEditAs());

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}

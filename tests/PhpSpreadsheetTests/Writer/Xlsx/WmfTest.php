<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class WmfTest extends AbstractFunctional
{
    /**
     * Test save and load XLSX file with wmf image.
     */
    public function testWmf(): void
    {
        // Read spreadsheet from file
        $inputFilename = 'tests/data/Writer/XLSX/wmffile.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($inputFilename);
        $drawings = $spreadsheet->getActiveSheet()->getDrawingCollection();
        self::assertCount(1, $drawings);
        $drawing = $drawings[0];
        self::assertInstanceOf(Drawing::class, $drawing);
        self::assertSame('wmf', $drawing->getExtension());

        // Save spreadsheet to file and read it back
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $drawings = $reloadedSpreadsheet->getActiveSheet()->getDrawingCollection();
        self::assertCount(1, $drawings);
        $drawing = $drawings[0];
        self::assertInstanceOf(Drawing::class, $drawing);
        self::assertSame('wmf', $drawing->getExtension());

        $spreadsheet->disconnectWorksheets();
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}

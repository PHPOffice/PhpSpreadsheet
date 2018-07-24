<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PHPUnit\Framework\TestCase;

class DrawingTest extends TestCase
{
    public function testCloningWorksheetWithImages()
    {
        $filename = './data/Reader/XLSX/with_images.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);
        $worksheet = $spreadsheet->getSheetByName('Invoice');
        $originDrawingCount = count($worksheet->getDrawingCollection());

        $clonedWorksheet = clone $worksheet;
        $clonedDrawingCount = count($clonedWorksheet->getDrawingCollection());
        self::assertEquals($originDrawingCount, $clonedDrawingCount);
    }
}

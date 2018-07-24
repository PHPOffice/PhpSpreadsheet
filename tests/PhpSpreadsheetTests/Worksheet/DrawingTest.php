<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PHPUnit\Framework\TestCase;

class DrawingTest extends TestCase
{
    public function testCloningWorksheetWithImages()
    {
        $filename = './data/Reader/Xls/with_images.xls';
        $reader = new Xls();
        $spreadsheet = $reader->load($filename);
        $worksheet = $spreadsheet->getSheetByName('Sheet1');
        $originDrawingCount = count($worksheet->getDrawingCollection());

        $clonedWorksheet = clone $worksheet;
        $clonedDrawingCount = count($clonedWorksheet->getDrawingCollection());
        self::assertEquals($originDrawingCount, $clonedDrawingCount);
    }
}

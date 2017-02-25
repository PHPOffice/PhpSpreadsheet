<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\CellsFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class CellCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testCacheLastCell()
    {
        $methods = CellsFactory::getCacheStorageMethods();
        foreach ($methods as $method) {
            CellsFactory::initialize($method);
            $workbook = new Spreadsheet();
            $cells = ['A1', 'A2'];
            $worksheet = $workbook->getActiveSheet();
            $worksheet->setCellValue('A1', 1);
            $worksheet->setCellValue('A2', 2);
            $this->assertEquals($cells, $worksheet->getCellCollection(), "Cache method \"$method\".");
            CellsFactory::finalize();
        }
    }
}

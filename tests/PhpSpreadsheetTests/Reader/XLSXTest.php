<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PHPUnit_Framework_TestCase;

class XLSXTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test load Xlsx file without cell reference.
     */
    public function testLoadXlsxWithoutCellReference()
    {
        $filename = './data/Reader/XLSX/without_cell_reference.xlsx';
        $reader = new Xlsx();
        $reader->load($filename);
    }
}

<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PHPUnit\Framework\TestCase;

class XlsTest extends TestCase
{
    /**
     * Test load Xls file without cell reference.
     */
    public function testLoadXlsWithoutCellReference()
    {
        $filename = './data/Reader/Xls/without_cell_reference.xls';
        $reader = new Xls();
        $reader->load($filename);
    }
}

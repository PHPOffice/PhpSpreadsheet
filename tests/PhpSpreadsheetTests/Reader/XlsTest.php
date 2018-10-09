<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PHPUnit\Framework\TestCase;

class XlsTest extends TestCase
{
    /**
     * Test load Xls file.
     */
    public function testLoadXlsSample()
    {
        $filename = './data/Reader/XLS/sample.xls';
        $reader = new Xls();
        $spreadsheet = $reader->load($filename);
        self::assertEquals('Title', $spreadsheet->getSheet(0)->getCell('A1')->getValue());
    }
}

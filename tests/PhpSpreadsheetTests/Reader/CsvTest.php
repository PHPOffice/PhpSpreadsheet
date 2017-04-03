<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class CsvTest extends \PHPUnit_Framework_TestCase
{
    public function testEnclosure()
    {
        $value = '<img alt="" src="http://example.com/image.jpg" />';
        $filename = tempnam(File::sysGetTempDir(), 'phpspreadsheet');

        // Write temp file with value
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->getCell('A1')->setValue($value);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
        $writer->save($filename);

        // Read written file
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        $reloadedSpreadsheet = $reader->load($filename);
        $actual = $reloadedSpreadsheet->getActiveSheet()->getCell('A1')->getCalculatedValue();
        $this->assertSame($value, $actual, 'should be able to write and read strings with multiples quotes');
    }
}

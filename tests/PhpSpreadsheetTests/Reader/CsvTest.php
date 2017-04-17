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

    public function testDelimiterDetection()
    {
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        $this->assertNull($reader->getDelimiter());

        $filename = __DIR__ . '/../../data/Reader/CSV/semicolon_separated.csv';
        $spreadsheet = $reader->load($filename);

        $this->assertSame(';', $reader->getDelimiter(), 'should be able to infer the delimiter');

        $actual = $spreadsheet->getActiveSheet()->getCell('C2')->getValue();
        $this->assertSame('25,5', $actual, 'should be able to retrieve values with commas');
    }
}

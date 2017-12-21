<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PHPUnit\Framework\TestCase;

class CsvTest extends TestCase
{
    public function testDelimiterDetection()
    {
        $reader = new Csv();
        self::assertNull($reader->getDelimiter());

        $filename = __DIR__ . '/../../data/Reader/CSV/semicolon_separated.csv';
        $spreadsheet = $reader->load($filename);

        self::assertSame(';', $reader->getDelimiter(), 'should be able to infer the delimiter');

        $actual = $spreadsheet->getActiveSheet()->getCell('C2')->getValue();
        self::assertSame('25,5', $actual, 'should be able to retrieve values with commas');
    }
}

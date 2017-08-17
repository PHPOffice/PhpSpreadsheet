<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Reader\Html;
use PHPUnit_Framework_TestCase;

class HTMLTest extends PHPUnit_Framework_TestCase
{
    public function testCsvWithAngleBracket()
    {
        $filename = __DIR__ . '/../../data/Reader/HTML/csv_with_angle_bracket.csv';
        $reader = new Html();
        $this->assertFalse($reader->canRead($filename));
    }
}

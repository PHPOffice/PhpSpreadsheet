<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Reader\HTML;

class HTMLTest extends \PHPUnit_Framework_TestCase
{
    public function testCsvWithAngleBracket()
    {
        $filename = __DIR__ . '/../../data/Reader/HTML/csv_with_angle_bracket.csv';
        $this->assertFalse($this->getInstance()->canRead($filename));
    }

    private function getInstance()
    {
        return new HTML();
    }
}

<?php

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class SpreadsheetTest extends TestCase
{
    /** @var Spreadsheet */
    private $object;

    public function setUp()
    {
        parent::setUp();
        $this->object = new Spreadsheet();
        $sheet = $this->object->getActiveSheet();

        $sheet->setTitle('someSheet1');
        $sheet = new Worksheet();
        $sheet->setTitle('someSheet2');
        $this->object->addSheet($sheet);
        $sheet = new Worksheet();
        $sheet->setTitle('someSheet 3');
        $this->object->addSheet($sheet);
    }

    /**
     * @return array
     */
    public function dataProviderForSheetNames()
    {
        $array = [
            [0, 'someSheet1'],
            [0, "'someSheet1'"],
            [1, 'someSheet2'],
            [1, "'someSheet2'"],
            [2, 'someSheet 3'],
            [2, "'someSheet 3'"],
        ];

        return $array;
    }

    /**
     * @param $index
     * @param $sheetName
     *
     * @dataProvider dataProviderForSheetNames
     */
    public function testGetSheetByName($index, $sheetName)
    {
        $this->assertEquals($this->object->getSheet($index), $this->object->getSheetByName($sheetName));
    }
}

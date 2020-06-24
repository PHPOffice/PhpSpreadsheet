<?php

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class SpreadsheetTest extends TestCase
{
    /** @var Spreadsheet */
    private $object;

    protected function setUp(): void
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
    public function testGetSheetByName($index, $sheetName): void
    {
        self::assertEquals($this->object->getSheet($index), $this->object->getSheetByName($sheetName));
    }

    /**
     * Test that after copy the source spreadsheet has a worksheet.
     *
     * @runInSeparateProcess
     */
    public function testCopySpreadsheet(): void
    {
        // Create new Spreadsheet object
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        // Check that the source has 1 sheet
        self::assertEquals(1, $spreadsheet->getSheetCount(), "The source spreadsheet doesn't contain 1 worksheet.");

        // Do the copy
        $copy = $spreadsheet->copy();

        // Check that the copy has 1 sheet
        self::assertEquals(1, $copy->getSheetCount(), "The copy spreadsheet doesn't contain 1 worksheet.");

        // Check that the source has 1 sheet
        self::assertEquals(1, $spreadsheet->getSheetCount(), "Spreadsheet copy failed: the source spreadsheet doesn't contain 1 worksheet.");
    }
}

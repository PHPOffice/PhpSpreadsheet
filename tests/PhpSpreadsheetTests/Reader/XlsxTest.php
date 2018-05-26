<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PHPUnit\Framework\TestCase;

class XlsxTest extends TestCase
{
    /**
     * Test load Xlsx file without cell reference.
     */
    public function testLoadXlsxWithoutCellReference()
    {
        $filename = './data/Reader/XLSX/without_cell_reference.xlsx';
        $reader = new Xlsx();

        $reader->setLazyInitCells(false);
        $spreadsheet = $reader->load($filename);
        $firstSheet = $spreadsheet->getSheet(0);
        self::assertEquals('2', $firstSheet->getCell('B2')->getValue());

        $reader->setLazyInitCells(true);
        $spreadsheet = $reader->load($filename);
        $firstSheet = $spreadsheet->getSheet(0);
        self::assertEquals('2', $firstSheet->getCell('B2')->getValue());
    }
}

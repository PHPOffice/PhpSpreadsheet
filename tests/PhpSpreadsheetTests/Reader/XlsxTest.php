<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
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
        $reader->load($filename);
    }

    /**
     * Test load Xlsx file with many empty cells (and big max row number) by readfilter.
     */
    public function testLoadXlsxWithManyEmptyCellsByReadFilter()
    {
        $filename = './data/Reader/XLSX/with_many_empty_cells.xlsx';

        /**
         * test WITHOUT reader filter.
         */
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getSheet(0);

        // test highest column (very specific num of columns because of some 3rd party software)
        self::assertSame('AMK', $sheet->getHighestColumn());

        // test highest row (very specific num of rows because of some 3rd party software)
        self::assertSame(1048576, $sheet->getHighestRow());

        // test top left coordinate
        $sortedCoordinates = $sheet->getCellCollection()->getSortedCoordinates();
        $coordinateTopLeft = reset($sortedCoordinates);
        self::assertSame('A1', $coordinateTopLeft);

        /**
         * test WITH reader filter.
         */
        $reader = new Xlsx();
        // Create a stub for the readFilter class.
        $readFilterStub = $this->createMock(IReadFilter::class);
        $readFilterStub->method('readCell')
            ->will($this->returnCallback([$this, 'readFilterReadCell']));
        // apply filter and load
        $reader->setReadFilter($readFilterStub);
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getSheet(0);

        // test highest column (very specific num of columns because of some 3rd party software)
        self::assertSame('D', $sheet->getHighestColumn());

        // test highest row (very specific num of rows because of some 3rd party software)
        self::assertSame(6, $sheet->getHighestRow());

        // test top left coordinate
        $sortedCoordinates = $sheet->getCellCollection()->getSortedCoordinates();
        $coordinateTopLeft = reset($sortedCoordinates);
        self::assertSame('B2', $coordinateTopLeft);
    }

    /**
     *  @see  \PhpOffice\PhpSpreadsheet\Reader\IReadFilter::readCell()
     *
     * @param mixed $column
     * @param mixed $row
     * @param mixed $worksheetName
     */
    public function readFilterReadCell($column, $row, $worksheetName = '')
    {
        // define filter range
        $rowMin = 2;
        $rowMax = 6;
        $columnMin = 'B';
        $columnMax = 'D';

        $r = (int) $row;
        if ($r > $rowMax || $r < $rowMin) {
            return false;
        }

        $col = sprintf('%04s', $column);
        if ($col > sprintf('%04s', $columnMax) ||
            $col < sprintf('%04s', $columnMin)) {
            return false;
        }

        return true;
    }
}

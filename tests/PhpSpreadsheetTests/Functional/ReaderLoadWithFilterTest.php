<?php

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ReaderLoadWithFilterTest extends AbstractFunctional
{
    public function providerCellsValues()
    {
        return [
                    [
                        [   // one argument as a multidimensional array
                            [1, 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'],
                            [2, 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'],
                            [3, 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'],
                            [4, 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'],
                            [5, 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'],
                            [6, 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'],
                            [7, 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'],
                            [8, 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'],
                            [9, 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'],
                            [10, 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'],
                        ],
                    ],
                ];
    }

    /**
     * Test load Xlsx file with many empty cells with no filter used.
     *
     * @dataProvider providerCellsValues
     *
     * @param  array  $arrayData [description]
     */
    public function testXlsxLoadWithoutReadFilter(array $arrayData)
    {
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->fromArray($arrayData, null, 'A1');

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $sheet = $reloadedSpreadsheet->getSheet(0);
        // test highest column (very specific num of columns because of some 3rd party software)
        self::assertSame('J', $sheet->getHighestColumn());

        // test highest row (very specific num of rows because of some 3rd party software)
        self::assertEquals(10, $sheet->getHighestRow());

        // test top left coordinate
        $sortedCoordinates = $sheet->getCellCollection()->getSortedCoordinates();
        $coordinateTopLeft = reset($sortedCoordinates);
        self::assertSame('A1', $coordinateTopLeft);
    }

    /**
     * Test load Xlsx file with many empty cells (and big max row number) with readfilter.
     *
     * @dataProvider providerCellsValues
     *
     * @param  array  $arrayData [description]
     */
    public function testXlsxLoadWithReadFilter(array $arrayData)
    {
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->fromArray($arrayData, null, 'A1');

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx', function ($reader) {
            // Create a stub for the readFilter class.
            $readFilterStub = $this->createMock(IReadFilter::class);
            $readFilterStub->method('readCell')
                ->will($this->returnCallback([$this, 'readFilterReadCell']));
            // apply filter
            $reader->setReadFilter($readFilterStub);
        });
        $sheet = $reloadedSpreadsheet->getSheet(0);
        // test highest column (very specific num of columns because of some 3rd party software)
        self::assertSame('D', $sheet->getHighestColumn());

        // test highest row (very specific num of rows because of some 3rd party software)
        self::assertEquals(6, $sheet->getHighestRow());

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

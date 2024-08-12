<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\Reader\IReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ReadFilterTest extends AbstractFunctional
{
    public static function providerCellsValues(): array
    {
        $cellValues = [
            // one argument as a multidimensional array
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
        ];

        return [
            ['Xlsx', $cellValues],
            ['Ods', $cellValues],
        ];
    }

    /**
     * Test load Xlsx file with many empty cells with no filter used.
     *
     * @dataProvider providerCellsValues
     */
    public function testXlsxLoadWithoutReadFilter(string $format, array $arrayData): void
    {
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->fromArray($arrayData, null, 'A1');

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format);
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
     */
    public function testXlsxLoadWithReadFilter(string $format, array $arrayData): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->fromArray($arrayData, null, 'A1');

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format, function (IReader $reader): void {
            // apply filter
            $reader->setReadFilter(new ReadFilterFilter());
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
}

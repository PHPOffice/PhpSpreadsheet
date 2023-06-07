<?php

namespace PhpOffice\PhpSpreadsheetTests\Custom;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\Assert;

trait SpreadsheetAssertionsTrait
{
    /**
     * Assert that the items of the passed value map are identical to the cell values of the worksheet.
     *
     * @example Basic usage: ```
     *     $this->assertSpreadsheetCellValuesMap([
     *         ['Firstname', 'Lastname', 'Age'],
     *         ['Christian', 'Miller', '35'],
     *         ['Tim', 'Lemon', '22'],
     *     ], $spreadsheet->getActiveSheet());
     * ```
     * @example You can ommit the comparison of a row by adding an empty array: ```
     *     $this->assertSpreadsheetCellValuesMap([
     *         [], // the first row won't be compared
     *         ['Christian', 'Miller', '35'],
     *         ['Tim', 'Lemon', '22'],
     *     ], $spreadsheet->getActiveSheet());
     * ```
     *
     * @param iterable $expectedValueMap two dimensional array or iterator map
     */
    public static function assertSpreadsheetCellValuesMap(iterable $expectedValueMap, Worksheet $sheet): void
    {
        $actualValues = $sheet->toArray();

        // Replace rows in the actual values if the expected row array is empty.
        foreach ($expectedValueMap as $rowIndex => $expectedCellValues) {
            if (0 === count($expectedCellValues)) {
                $actualValues[$rowIndex] = [];
            }
        }

        Assert::assertSame($expectedValueMap, $actualValues);
    }
}

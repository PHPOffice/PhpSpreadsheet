<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PHPUnit\Framework\TestCase;

/**
 * Sanity tests for functions which have been moved out of LookupRef
 * to their own classes. A deprecated version remains in LookupRef;
 * this class contains cursory tests to ensure that those work properly.
 * Only 6 of the 15 functions are covered below at the moment.
 *
 * @covers \PhpOffice\PhpSpreadsheet\Calculation\LookupRef
 */
class MovedFunctionsTest extends TestCase
{
    public function testMovedFunctions(): void
    {
        self::assertSame('$G$3', /** @scrutinizer ignore-deprecated */ LookupRef::cellAddress(3, 7));
        self::assertSame(3, /** @scrutinizer ignore-deprecated */ LookupRef::COLUMN('C5'));
        self::assertSame(2, /** @scrutinizer ignore-deprecated */ LookupRef::COLUMNS([[1, 2], [3, 5], [7, 9]]));
        self::assertSame(30, /** @scrutinizer ignore-deprecated */ LookupRef::CHOOSE(3, 10, 20, 30, 40));
        self::assertSame(ExcelError::REF(), /** @scrutinizer ignore-deprecated */ LookupRef::FORMULATEXT('A1'));
        self::assertSame(ExcelError::REF(), /** @scrutinizer ignore-deprecated */ LookupRef::HYPERLINK('https://phpspreadsheet.readthedocs.io/en/latest/', 'Read the Docs'));
        self::assertSame(60, /** @scrutinizer ignore-deprecated */ LookupRef::INDEX([[10, 20, 30], [40, 50, 60], [70, 80, 90]], 2, 3));
        self::assertSame(3, /** @scrutinizer ignore-deprecated */ LookupRef::MATCH(3, [1, 10, 3, 8], 0));
        self::assertSame('#VALUE!', /** @scrutinizer ignore-deprecated */ LookupRef::OFFSET(null));
        self::assertSame(5, /** @scrutinizer ignore-deprecated */ LookupRef::ROW('C5'));
        self::assertSame(3, /** @scrutinizer ignore-deprecated */ LookupRef::ROWS([[1, 2], [3, 5], [7, 9]]));
        self::assertSame([[1, 2], [3, 4]], /** @scrutinizer ignore-deprecated */ LookupRef::TRANSPOSE([[1, 3], [2, 4]]));
    }

    public function testLookup(): void
    {
        $densityGrid = [
            ['Density', 'Viscosity', 'Temperature'],
            [0.457, 3.55, 500],
            [0.525, 3.25, 400],
            [0.616, 2.93, 300],
            [0.675, 2.75, 250],
            [0.746, 2.57, 200],
            [0.835, 2.38, 150],
            [0.946, 2.17, 100],
            [1.090, 1.95, 50],
            [1.290, 1.71, 0],
        ];
        $expectedResult = 100;
        $result = /** @scrutinizer ignore-deprecated */ LookupRef::VLOOKUP(1, $densityGrid, 3, true);
        self::assertSame($expectedResult, $result);
        $orderGrid = [
            ['Order ID', 10247, 10249, 10250, 10251, 10252, 10253],
            ['Unit Price', 14.00, 18.60, 7.70, 16.80, 16.80, 64.80],
            ['Quantity', 12, 9, 10, 6, 20, 40],
        ];
        $expectedResult = 16.80;
        $result = /** @scrutinizer ignore-deprecated */ LookupRef::HLOOKUP(10251, $orderGrid, 2, false);
        self::assertSame($expectedResult, $result);
        $array1 = [
            [4.14],
            [4.19],
            [5.17],
            [5.77],
            [6, 39],
        ];
        $array2 = [
            ['red'],
            ['orange'],
            ['yellow'],
            ['green'],
            ['blue'],
        ];
        $expectedResult = 'orange';
        $result = /** @scrutinizer ignore-deprecated */ LookupRef::LOOKUP(4.19, $array1, $array2);
        self::assertSame($expectedResult, $result);
    }

    public function testGrandfatheredHlookup(): void
    {
        // Second parameter is supposed to be array of arrays.
        // Some old tests called function directly using array of strings;
        // ensure these work as before.
        $expectedResult = '#REF!';
        $result = /** @scrutinizer ignore-deprecated */ LookupRef::HLOOKUP(
            'Selection column',
            ['Selection column', 'Value to retrieve'],
            5,
            false
        );
        self::assertSame($expectedResult, $result);
        $expectedResult = 'Value to retrieve';
        $result = /** @scrutinizer ignore-deprecated */ LookupRef::HLOOKUP(
            'Selection column',
            ['Selection column', 'Value to retrieve'],
            2,
            false
        );
        self::assertSame($expectedResult, $result);
    }
}

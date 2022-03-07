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
        self::assertSame(3, LookupRef::COLUMN('C5'));
        self::assertSame(ExcelError::REF(), LookupRef::FORMULATEXT('A1'));
        self::assertSame(ExcelError::REF(), LookupRef::HYPERLINK('https://phpspreadsheet.readthedocs.io/en/latest/', 'Read the Docs'));
        self::assertSame('#VALUE!', LookupRef::OFFSET(null));
        self::assertSame(5, LookupRef::ROW('C5'));
        self::assertSame([[1, 2], [3, 4]], LookupRef::TRANSPOSE([[1, 3], [2, 4]]));
    }
}

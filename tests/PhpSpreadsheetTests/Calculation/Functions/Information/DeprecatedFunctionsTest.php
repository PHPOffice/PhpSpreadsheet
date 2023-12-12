<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Information;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

// Sanity tests for functions which have been moved out of Functions
// to their own classes. A deprecated version remains in Functions;
// this class contains cursory tests to ensure that those work properly.

class DeprecatedFunctionsTest extends TestCase
{
    public function testDeprecated(): void
    {
        self::assertSame('#DIV/0!', Functions::DIV0());
        self::assertSame('#N/A', Functions::errorType());
        self::assertTrue(Functions::isBlank());
        self::assertTrue(Functions::isErr('#DIV/0!'));
        self::assertTrue(Functions::isError('#DIV/0!'));
        self::assertTrue(Functions::isEven(2));
        // isFormula needs more complicated test - see next method
        self::assertFalse(Functions::isNa());
        self::assertFalse(Functions::isLogical());
        self::assertTrue(Functions::isNonText(2));
        self::assertTrue(Functions::isNumber(2));
        self::assertFalse(Functions::isOdd(2));
        self::assertFalse(Functions::isText(2));
        self::assertSame('#N/A', Functions::NA());
        self::assertSame('#NAME?', Functions::NAME());
        self::assertSame('#NUM!', Functions::NAN());
        self::assertSame(1, Functions::n(true));
        self::assertSame('#NULL!', Functions::null());
        self::assertSame('#REF!', Functions::REF());
        self::assertSame(1, Functions::type(7));
        self::assertSame('#VALUE!', Functions::VALUE());
    }

    public function testIsFormula(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cell = $sheet->getCell('A1');
        $cell->setValue('=1');
        self::assertTrue(Functions::isFormula('A1', $cell));
        $spreadsheet->disconnectWorksheets();
    }
}

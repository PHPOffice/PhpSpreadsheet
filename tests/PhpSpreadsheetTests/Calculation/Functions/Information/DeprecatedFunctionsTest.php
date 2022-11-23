<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Information;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

// Sanity tests for functions which have been moved out of Functions
// to their own classes. A deprecated version remains in Functions;
// this class contains cursory tests to ensure that those work properly.
// If Scrutinizer fails the PR because of these deprecations, I will
// remove this class from the PR.

class DeprecatedFunctionsTest extends TestCase
{
    public function testDeprecated(): void
    {
        self::assertSame('#DIV/0!', /** @scrutinizer ignore-deprecated */ Functions::DIV0());
        self::assertSame('#N/A', /** @scrutinizer ignore-deprecated */ Functions::errorType());
        self::assertTrue(/** @scrutinizer ignore-deprecated */ Functions::isBlank());
        self::assertTrue(/** @scrutinizer ignore-deprecated */ Functions::isErr('#DIV/0!'));
        self::assertTrue(/** @scrutinizer ignore-deprecated */ Functions::isError('#DIV/0!'));
        self::assertTrue(/** @scrutinizer ignore-deprecated */ Functions::isEven(2));
        // isFormula needs more complicated test - see next method
        self::assertFalse(/** @scrutinizer ignore-deprecated */ Functions::isNa());
        self::assertFalse(/** @scrutinizer ignore-deprecated */ Functions::isLogical());
        self::assertTrue(/** @scrutinizer ignore-deprecated */ Functions::isNonText(2));
        self::assertTrue(/** @scrutinizer ignore-deprecated */ Functions::isNumber(2));
        self::assertFalse(/** @scrutinizer ignore-deprecated */ Functions::isOdd(2));
        self::assertFalse(/** @scrutinizer ignore-deprecated */ Functions::isText(2));
        self::assertSame('#N/A', /** @scrutinizer ignore-deprecated */ Functions::NA());
        self::assertSame('#NAME?', /** @scrutinizer ignore-deprecated */ Functions::NAME());
        self::assertSame('#NUM!', /** @scrutinizer ignore-deprecated */ Functions::NAN());
        self::assertSame(1, /** @scrutinizer ignore-deprecated */ Functions::n(true));
        self::assertSame('#NULL!', /** @scrutinizer ignore-deprecated */ Functions::null());
        self::assertSame('#REF!', /** @scrutinizer ignore-deprecated */ Functions::REF());
        self::assertSame(1, /** @scrutinizer ignore-deprecated */ Functions::type(7));
        self::assertSame('#VALUE!', /** @scrutinizer ignore-deprecated */ Functions::VALUE());
    }

    public function testIsFormula(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cell = $sheet->getCell('A1');
        $cell->setValue('=1');
        self::assertTrue(/** @scrutinizer ignore-deprecated */ Functions::isFormula('A1', $cell));
        $spreadsheet->disconnectWorksheets();
    }
}

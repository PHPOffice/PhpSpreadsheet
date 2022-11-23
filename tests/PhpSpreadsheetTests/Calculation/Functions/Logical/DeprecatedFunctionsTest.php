<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\Logical;
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
        self::assertFalse(/** @scrutinizer ignore-deprecated */ Logical::false());
        self::assertFalse(/** @scrutinizer ignore-deprecated */ Logical::logicalAnd(true, false));
        self::assertTrue(/** @scrutinizer ignore-deprecated */ Logical::NOT(false));
        self::assertTrue(/** @scrutinizer ignore-deprecated */ Logical::logicalOr(true, false));
        self::assertTrue(/** @scrutinizer ignore-deprecated */ Logical::logicalXor(true, false));
        self::assertTrue(/** @scrutinizer ignore-deprecated */ Logical::true());
        self::assertFalse(/** @scrutinizer ignore-deprecated */ Logical::statementIf(false));
        self::assertSame('error', /** @scrutinizer ignore-deprecated */ Logical::IFERROR('#VALUE!', 'error'));
        self::assertSame('#VALUE!', /** @scrutinizer ignore-deprecated */ Logical::IFNA('#VALUE!', 'error'));
        self::assertSame('two', /** @scrutinizer ignore-deprecated */ Logical::IFS(false, 'one', true, 'two', true, 'three'));
        self::assertSame(31, /** @scrutinizer ignore-deprecated */ Logical::statementSwitch(30, 10, 11, 20, 21, 30, 31, 40, 41));
    }
}

<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\Logical;
use PHPUnit\Framework\TestCase;

// Sanity tests for functions which have been moved out of Functions
// to their own classes. A deprecated version remains in Functions;
// this class contains cursory tests to ensure that those work properly.

class DeprecatedFunctionsTest extends TestCase
{
    public function testDeprecated(): void
    {
        self::assertFalse(Logical::false());
        self::assertFalse(Logical::logicalAnd(true, false));
        self::assertTrue(Logical::NOT(false));
        self::assertTrue(Logical::logicalOr(true, false));
        self::assertTrue(Logical::logicalXor(true, false));
        self::assertTrue(Logical::true());
        self::assertFalse(Logical::statementIf(false));
        self::assertSame('error', Logical::IFERROR('#VALUE!', 'error'));
        self::assertSame('#VALUE!', Logical::IFNA('#VALUE!', 'error'));
        self::assertSame('two', Logical::IFS(false, 'one', true, 'two', true, 'three'));
        self::assertSame(31, Logical::statementSwitch(30, 10, 11, 20, 21, 30, 31, 40, 41));
    }
}

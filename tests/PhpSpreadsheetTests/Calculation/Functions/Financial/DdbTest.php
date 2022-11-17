<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class DdbTest extends TestCase
{
    /**
     * @dataProvider providerDDB
     *
     * @param mixed $expectedResult
     */
    public function testDDB($expectedResult, ...$args): void
    {
        $result = Financial\Depreciation::DDB(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerDDB(): array
    {
        return require 'tests/data/Calculation/Financial/DDB.php';
    }
}

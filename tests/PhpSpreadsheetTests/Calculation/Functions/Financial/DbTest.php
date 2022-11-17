<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class DbTest extends TestCase
{
    /**
     * @dataProvider providerDB
     *
     * @param mixed $expectedResult
     */
    public function testDB($expectedResult, ...$args): void
    {
        $result = Financial\Depreciation::DB(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerDB(): array
    {
        return require 'tests/data/Calculation/Financial/DB.php';
    }
}

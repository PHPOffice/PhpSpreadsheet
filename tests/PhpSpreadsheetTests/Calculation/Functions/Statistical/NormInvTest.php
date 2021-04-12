<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class NormInvTest extends TestCase
{
    /**
     * @dataProvider providerNORMINV
     *
     * @param mixed $expectedResult
     */
    public function testNORMINV($expectedResult, ...$args): void
    {
        $result = Statistical::NORMINV(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerNORMINV(): array
    {
        return require 'tests/data/Calculation/Statistical/NORMINV.php';
    }
}

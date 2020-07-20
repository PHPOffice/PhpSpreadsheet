<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class NormDistTest extends TestCase
{
    /**
     * @dataProvider providerNORMDIST
     *
     * @param mixed $expectedResult
     */
    public function testNORMDIST($expectedResult, ...$args): void
    {
        $result = Statistical::NORMDIST(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerNORMDIST(): array
    {
        return require 'tests/data/Calculation/Statistical/NORMDIST.php';
    }
}

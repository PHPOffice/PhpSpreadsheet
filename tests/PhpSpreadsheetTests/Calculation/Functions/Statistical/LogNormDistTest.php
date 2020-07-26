<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class LogNormDistTest extends TestCase
{
    /**
     * @dataProvider providerLOGNORMDIST
     *
     * @param mixed $expectedResult
     */
    public function testLOGNORMDIST($expectedResult, ...$args): void
    {
        $result = Statistical::LOGNORMDIST(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerLOGNORMDIST(): array
    {
        return require 'tests/data/Calculation/Statistical/LOGNORMDIST.php';
    }
}

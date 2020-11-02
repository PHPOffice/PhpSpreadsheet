<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class MinATest extends TestCase
{
    /**
     * @dataProvider providerMINA
     *
     * @param mixed $expectedResult
     */
    public function testMINA($expectedResult, ...$args): void
    {
        $result = Statistical::MINA(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerMINA(): array
    {
        return require 'tests/data/Calculation/Statistical/MINA.php';
    }
}

<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class NormSDist2Test extends TestCase
{
    /**
     * @dataProvider providerNORMSDIST2
     *
     * @param mixed $expectedResult
     */
    public function testNORMSDIST2($expectedResult, ...$args): void
    {
        $result = Statistical::NORMSDIST2(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerNORMSDIST2(): array
    {
        return require 'tests/data/Calculation/Statistical/NORMSDIST2.php';
    }
}

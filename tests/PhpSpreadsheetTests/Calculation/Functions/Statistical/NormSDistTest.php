<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class NormSDistTest extends TestCase
{
    /**
     * @dataProvider providerNORMSDIST
     *
     * @param mixed $expectedResult
     */
    public function testNORMSDIST2($expectedResult, ...$args): void
    {
        $result = Statistical::NORMSDIST(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerNORMSDIST()
    {
        return require 'tests/data/Calculation/Statistical/NORMSDIST.php';
    }
}

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
     * @param mixed $testValue
     */
    public function testNORMSDIST($expectedResult, $testValue): void
    {
        $result = Statistical::NORMSDIST($testValue);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerNORMSDIST(): array
    {
        return require 'tests/data/Calculation/Statistical/NORMSDIST.php';
    }
}

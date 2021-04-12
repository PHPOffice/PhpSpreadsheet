<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class ZTestTest extends TestCase
{
    /**
     * @dataProvider providerZTEST
     *
     * @param mixed $expectedResult
     * @param mixed $value
     * @param mixed $dataSet
     * @param null|mixed $sigma
     */
    public function testZTEST($expectedResult, $dataSet, $value, $sigma = null): void
    {
        $result = Statistical::ZTEST($dataSet, $value, $sigma);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerZTEST(): array
    {
        return require 'tests/data/Calculation/Statistical/ZTEST.php';
    }
}

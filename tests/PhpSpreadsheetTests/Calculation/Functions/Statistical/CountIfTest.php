<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class CountIfTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCOUNTIF
     *
     * @param mixed $expectedResult
     */
    public function testCOUNTIF($expectedResult, ...$args): void
    {
        $result = Statistical::COUNTIF(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerCOUNTIF(): array
    {
        return require 'tests/data/Calculation/Statistical/COUNTIF.php';
    }
}

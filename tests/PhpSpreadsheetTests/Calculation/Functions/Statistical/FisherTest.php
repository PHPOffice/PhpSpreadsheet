<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class FisherTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerFISHER
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testFISHER($expectedResult, $value): void
    {
        $result = Statistical::FISHER($value);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerFISHER(): array
    {
        return require 'tests/data/Calculation/Statistical/FISHER.php';
    }
}

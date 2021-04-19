<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class SydTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerSYD
     *
     * @param mixed $expectedResult
     */
    public function testSYD($expectedResult, array $args): void
    {
        $result = Financial::SYD(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerSYD(): array
    {
        return require 'tests/data/Calculation/Financial/SYD.php';
    }
}

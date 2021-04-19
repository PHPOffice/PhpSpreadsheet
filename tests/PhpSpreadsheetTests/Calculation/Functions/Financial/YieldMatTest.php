<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class YieldMatTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerYIELDMAT
     *
     * @param mixed $expectedResult
     */
    public function testYIELDMAT($expectedResult, ...$args): void
    {
        $result = Financial::YIELDMAT(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerYIELDMAT(): array
    {
        return require 'tests/data/Calculation/Financial/YIELDMAT.php';
    }
}

<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class PvTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerPV
     *
     * @param mixed $expectedResult
     */
    public function testPV($expectedResult, array $args): void
    {
        $result = Financial::PV(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerPV(): array
    {
        return require 'tests/data/Calculation/Financial/PV.php';
    }
}

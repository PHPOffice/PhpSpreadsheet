<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class EffectTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerEFFECT
     *
     * @param mixed $expectedResult
     */
    public function testEFFECT($expectedResult, ...$args): void
    {
        $result = Financial::EFFECT(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerEFFECT()
    {
        return require 'tests/data/Calculation/Financial/EFFECT.php';
    }
}

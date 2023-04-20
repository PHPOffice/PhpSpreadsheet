<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class EffectTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerEFFECT
     *
     * @param mixed $expectedResult
     */
    public function testEFFECT($expectedResult, ...$args): void
    {
        $this->runTestCase('EFFECT', $expectedResult, $args);
    }

    public static function providerEFFECT(): array
    {
        return require 'tests/data/Calculation/Financial/EFFECT.php';
    }
}

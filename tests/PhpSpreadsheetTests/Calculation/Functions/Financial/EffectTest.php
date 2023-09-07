<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class EffectTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerEFFECT
     */
    public function testEFFECT(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('EFFECT', $expectedResult, $args);
    }

    public static function providerEFFECT(): array
    {
        return require 'tests/data/Calculation/Financial/EFFECT.php';
    }
}

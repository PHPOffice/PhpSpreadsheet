<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class NominalTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerNOMINAL
     */
    public function testNOMINAL(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('NOMINAL', $expectedResult, $args);
    }

    public static function providerNOMINAL(): array
    {
        return require 'tests/data/Calculation/Financial/NOMINAL.php';
    }
}

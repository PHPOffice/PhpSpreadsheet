<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class CoupNumTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCOUPNUM
     */
    public function testCOUPNUM(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('COUPNUM', $expectedResult, $args);
    }

    public static function providerCOUPNUM(): array
    {
        return require 'tests/data/Calculation/Financial/COUPNUM.php';
    }
}

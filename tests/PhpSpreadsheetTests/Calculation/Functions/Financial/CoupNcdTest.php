<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class CoupNcdTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCOUPNCD
     */
    public function testCOUPNCD(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('COUPNCD', $expectedResult, $args);
    }

    public static function providerCOUPNCD(): array
    {
        return require 'tests/data/Calculation/Financial/COUPNCD.php';
    }
}

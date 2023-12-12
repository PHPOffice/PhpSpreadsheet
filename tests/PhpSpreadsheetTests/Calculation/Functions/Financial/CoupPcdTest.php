<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class CoupPcdTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCOUPPCD
     */
    public function testCOUPPCD(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('COUPPCD', $expectedResult, $args);
    }

    public static function providerCOUPPCD(): array
    {
        return require 'tests/data/Calculation/Financial/COUPPCD.php';
    }
}

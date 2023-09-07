<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class TBillYieldTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerTBILLYIELD
     */
    public function testTBILLYIELD(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('TBILLYIELD', $expectedResult, $args);
    }

    public static function providerTBILLYIELD(): array
    {
        return require 'tests/data/Calculation/Financial/TBILLYIELD.php';
    }
}

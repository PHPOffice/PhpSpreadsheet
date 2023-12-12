<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class UsDollarTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerUSDOLLAR
     */
    public function testUSDOLLAR(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('USDOLLAR', $expectedResult, $args);
    }

    public static function providerUSDOLLAR(): array
    {
        return require 'tests/data/Calculation/Financial/USDOLLAR.php';
    }
}

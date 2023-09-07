<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class CoupDaysTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCOUPDAYS
     */
    public function testCOUPDAYS(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('COUPDAYS', $expectedResult, $args);
    }

    public static function providerCOUPDAYS(): array
    {
        return require 'tests/data/Calculation/Financial/COUPDAYS.php';
    }
}

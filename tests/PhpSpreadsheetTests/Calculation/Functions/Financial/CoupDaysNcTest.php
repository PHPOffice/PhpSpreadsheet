<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class CoupDaysNcTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCOUPDAYSNC
     */
    public function testCOUPDAYSNC(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('COUPDAYSNC', $expectedResult, $args);
    }

    public static function providerCOUPDAYSNC(): array
    {
        return require 'tests/data/Calculation/Financial/COUPDAYSNC.php';
    }
}

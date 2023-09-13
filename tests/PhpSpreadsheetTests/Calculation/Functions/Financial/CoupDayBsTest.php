<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class CoupDayBsTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCOUPDAYBS
     */
    public function testCOUPDAYBS(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('COUPDAYBS', $expectedResult, $args);
    }

    public static function providerCOUPDAYBS(): array
    {
        return require 'tests/data/Calculation/Financial/COUPDAYBS.php';
    }
}

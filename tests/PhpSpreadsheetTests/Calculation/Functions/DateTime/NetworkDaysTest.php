<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Networkdays;

class NetworkDaysTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerNETWORKDAYS
     *
     * @param mixed $expectedResult
     */
    public function testNETWORKDAYS($expectedResult, ...$args): void
    {
        $result = Networkdays::funcNetworkdays(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerNETWORKDAYS()
    {
        return require 'tests/data/Calculation/DateTime/NETWORKDAYS.php';
    }
}

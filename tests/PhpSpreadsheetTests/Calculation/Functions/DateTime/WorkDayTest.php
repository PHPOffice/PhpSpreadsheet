<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\DateTime;

class WorkDayTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerWORKDAY
     *
     * @param mixed $expectedResult
     */
    public function testWORKDAY($expectedResult, ...$args): void
    {
        $result = DateTime::WORKDAY(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerWORKDAY()
    {
        return require 'tests/data/Calculation/DateTime/WORKDAY.php';
    }
}

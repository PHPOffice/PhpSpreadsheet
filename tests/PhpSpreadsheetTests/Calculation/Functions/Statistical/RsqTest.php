<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

// TODO Run test in spreadsheet context.
class RsqTest extends TestCase
{
    /**
     * @dataProvider providerRSQ
     *
     * @param mixed $expectedResult
     */
    public function testRSQ($expectedResult, array $xargs, array $yargs): void
    {
        $result = Statistical\Trends::RSQ($xargs, $yargs);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerRSQ(): array
    {
        return require 'tests/data/Calculation/Statistical/RSQ.php';
    }
}

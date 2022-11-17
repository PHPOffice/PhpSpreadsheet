<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class IPmtTest extends TestCase
{
    /**
     * @dataProvider providerIPMT
     *
     * @param mixed $expectedResult
     */
    public function testIPMT($expectedResult, array $args): void
    {
        $result = Financial\CashFlow\Constant\Periodic\Interest::payment(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerIPMT(): array
    {
        return require 'tests/data/Calculation/Financial/IPMT.php';
    }
}

<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class DollarFrTest extends TestCase
{
    /**
     * @dataProvider providerDOLLARFR
     *
     * @param mixed $expectedResult
     */
    public function testDOLLARFR($expectedResult, ...$args): void
    {
        if (count($args) === 0) {
            $result = Financial::DOLLARFR();
        } elseif (count($args) === 1) {
            $result = Financial::DOLLARFR($args[0]);
        } else {
            $result = Financial::DOLLARFR($args[0], $args[1]);
        }
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerDOLLARFR(): array
    {
        return require 'tests/data/Calculation/Financial/DOLLARFR.php';
    }
}

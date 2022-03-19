<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class PvTest extends TestCase
{
    /**
     * @dataProvider providerPV
     *
     * @param mixed $expectedResult
     */
    public function testPV($expectedResult, array $args): void
    {
        if (count($args) === 0) {
            $result = Financial::PV();
        } elseif (count($args) === 1) {
            $result = Financial::PV($args[0]);
        } elseif (count($args) === 2) {
            $result = Financial::PV($args[0], $args[1]);
        } elseif (count($args) === 3) {
            $result = Financial::PV($args[0], $args[1], $args[2]);
        } elseif (count($args) === 4) {
            $result = Financial::PV($args[0], $args[1], $args[2], $args[3]);
        } else {
            $result = Financial::PV($args[0], $args[1], $args[2], $args[3], $args[4]);
        }
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerPV(): array
    {
        return require 'tests/data/Calculation/Financial/PV.php';
    }
}

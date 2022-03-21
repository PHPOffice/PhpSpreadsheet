<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class NPerTest extends TestCase
{
    /**
     * @dataProvider providerNPER
     *
     * @param mixed $expectedResult
     */
    public function testNPER($expectedResult, array $args): void
    {
        if (count($args) === 0) {
            $result = Financial::NPER();
        } elseif (count($args) === 1) {
            $result = Financial::NPER($args[0]);
        } elseif (count($args) === 2) {
            $result = Financial::NPER($args[0], $args[1]);
        } elseif (count($args) === 3) {
            $result = Financial::NPER($args[0], $args[1], $args[2]);
        } elseif (count($args) === 4) {
            $result = Financial::NPER($args[0], $args[1], $args[2], $args[3]);
        } else {
            $result = Financial::NPER($args[0], $args[1], $args[2], $args[3], $args[4]);
        }
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerNPER(): array
    {
        return require 'tests/data/Calculation/Financial/NPER.php';
    }
}

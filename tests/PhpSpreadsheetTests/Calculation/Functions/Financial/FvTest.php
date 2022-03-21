<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class FvTest extends TestCase
{
    /**
     * @dataProvider providerFV
     *
     * @param mixed $expectedResult
     */
    public function testFV($expectedResult, array $args): void
    {
        if (count($args) === 0) {
            $result = Financial::FV();
        } elseif (count($args) === 1) {
            $result = Financial::FV($args[0]);
        } elseif (count($args) === 2) {
            $result = Financial::FV($args[0], $args[1]);
        } elseif (count($args) === 3) {
            $result = Financial::FV($args[0], $args[1], $args[2]);
        } elseif (count($args) === 4) {
            $result = Financial::FV($args[0], $args[1], $args[2], $args[3]);
        } else {
            $result = Financial::FV($args[0], $args[1], $args[2], $args[3], $args[4]);
        }
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerFV(): array
    {
        return require 'tests/data/Calculation/Financial/FV.php';
    }
}

<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic\Payments;
use PHPUnit\Framework\TestCase;

class PmtTest extends TestCase
{
    /**
     * @dataProvider providerPMT
     *
     * @param mixed $expectedResult
     */
    public function testPMT($expectedResult, array $args): void
    {
        $interestRate = array_shift($args);
        $numberOfPeriods = array_shift($args);
        $presentValue = array_shift($args);
        if (count($args) === 0) {
            $result = Payments::annuity($interestRate, $numberOfPeriods, $presentValue);
        } elseif (count($args) === 1) {
            $result = Payments::annuity($interestRate, $numberOfPeriods, $presentValue, $args[0]);
        } else {
            $result = Payments::annuity($interestRate, $numberOfPeriods, $presentValue, $args[0], $args[1]);
        }
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerPMT(): array
    {
        return require 'tests/data/Calculation/Financial/PMT.php';
    }
}

<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class PmtTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

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
        $result = Financial::PMT($interestRate, $numberOfPeriods, $presentValue, ...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerPMT(): array
    {
        return require 'tests/data/Calculation/Financial/PMT.php';
    }
}

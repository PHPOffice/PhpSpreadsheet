<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class ReceivedTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerRECEIVED
     *
     * @param mixed $expectedResult
     */
    public function testRECEIVED($expectedResult, ...$args): void
    {
        $result = Financial::RECEIVED(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerRECEIVED(): array
    {
        return require 'tests/data/Calculation/Financial/RECEIVED.php';
    }
}

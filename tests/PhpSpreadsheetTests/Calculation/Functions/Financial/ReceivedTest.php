<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class ReceivedTest extends TestCase
{
    /**
     * @dataProvider providerRECEIVED
     *
     * @param mixed $expectedResult
     */
    public function testRECEIVED($expectedResult, ...$args): void
    {
        $result = Financial\Securities\Price::received(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerRECEIVED(): array
    {
        return require 'tests/data/Calculation/Financial/RECEIVED.php';
    }
}

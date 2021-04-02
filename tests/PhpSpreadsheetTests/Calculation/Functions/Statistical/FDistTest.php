<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class FDistTest extends TestCase
{
    /**
     * @dataProvider providerFDIST
     *
     * @param mixed $expectedResult
     */
    public function testFDIST($expectedResult, ...$args): void
    {
        $result = Statistical::FDIST2(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerFDIST(): array
    {
        return require 'tests/data/Calculation/Statistical/FDIST.php';
    }
}

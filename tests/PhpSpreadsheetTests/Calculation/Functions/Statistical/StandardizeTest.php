<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class StandardizeTest extends TestCase
{
    /**
     * @dataProvider providerSTANDARDIZE
     *
     * @param mixed $expectedResult
     */
    public function testSTANDARDIZE($expectedResult, ...$args): void
    {
        $result = Statistical::STANDARDIZE(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerSTANDARDIZE(): array
    {
        return require 'tests/data/Calculation/Statistical/STANDARDIZE.php';
    }
}

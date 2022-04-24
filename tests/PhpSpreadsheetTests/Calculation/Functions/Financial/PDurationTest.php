<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Single;
use PHPUnit\Framework\TestCase;

class PDurationTest extends TestCase
{
    /**
     * @dataProvider providerPDURATION
     *
     * @param mixed $expectedResult
     */
    public function testPDURATION($expectedResult, array $args = []): void
    {
        $result = Single::periods(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerPDURATION(): array
    {
        return require 'tests/data/Calculation/Financial/PDURATION.php';
    }
}

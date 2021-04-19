<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class PriceTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerPRICE
     *
     * @param mixed $expectedResult
     */
    public function testPRICE($expectedResult, ...$args): void
    {
        $result = Financial::PRICE(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-7);
    }

    public function providerPRICE(): array
    {
        return require 'tests/data/Calculation/Financial/PRICE.php';
    }

    /**
     * @dataProvider providerPRICE3
     *
     * @param mixed $expectedResult
     */
    public function testPRICE3($expectedResult, ...$args): void
    {
        // These results (PRICE function with basis codes 2 and 3)
        // agree with published algorithm, LibreOffice, and Gnumeric.
        // They do not agree with Excel.
        $result = Financial::PRICE(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-7);
    }

    public function providerPRICE3(): array
    {
        return require 'tests/data/Calculation/Financial/PRICE3.php';
    }
}

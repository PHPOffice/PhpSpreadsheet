<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class NominalTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerNOMINAL
     *
     * @param mixed $expectedResult
     */
    public function testNOMINAL($expectedResult, ...$args): void
    {
        $result = Financial::NOMINAL(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerNOMINAL()
    {
        return require 'tests/data/Calculation/Financial/NOMINAL.php';
    }
}

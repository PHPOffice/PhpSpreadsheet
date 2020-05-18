<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class BitXorTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerBITXOR
     *
     * @param mixed $expectedResult
     * @param mixed[] $args
     */
    public function testBITXOR($expectedResult, array $args): void
    {
        $result = Engineering::BITXOR(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerBITXOR()
    {
        return require 'tests/data/Calculation/Engineering/BITXOR.php';
    }
}

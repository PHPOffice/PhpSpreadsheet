<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class BitAndTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerBITAND
     *
     * @param mixed $expectedResult
     * @param mixed[] $args
     */
    public function testBITAND($expectedResult, array $args): void
    {
        $result = Engineering::BITAND(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerBITAND()
    {
        return require 'tests/data/Calculation/Engineering/BITAND.php';
    }
}

<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class ChiDistRightTailTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCHIDIST
     *
     * @param mixed $expectedResult
     */
    public function testCHIDIST($expectedResult, ...$args): void
    {
        $result = Statistical::CHIDIST(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerCHIDIST(): array
    {
        return require 'tests/data/Calculation/Statistical/CHIDISTRightTail.php';
    }
}

<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Logical;
use PHPUnit\Framework\TestCase;

class XorTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerXOR
     *
     * @param mixed $expectedResult
     */
    public function testXOR($expectedResult, ...$args): void
    {
        $result = Logical::logicalXor(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerXOR(): array
    {
        return require 'tests/data/Calculation/Logical/XOR.php';
    }
}

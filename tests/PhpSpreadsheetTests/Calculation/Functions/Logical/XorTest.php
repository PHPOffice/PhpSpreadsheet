<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\Logical;
use PHPUnit\Framework\TestCase;

class XorTest extends TestCase
{
    /**
     * @dataProvider providerXOR
     *
     * @param mixed $expectedResult
     */
    public function testXOR($expectedResult, ...$args): void
    {
        $result = Logical\Operations::logicalXor(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerXOR(): array
    {
        return require 'tests/data/Calculation/Logical/XOR.php';
    }
}

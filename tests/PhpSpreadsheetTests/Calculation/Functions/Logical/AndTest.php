<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\Logical;
use PHPUnit\Framework\TestCase;

class AndTest extends TestCase
{
    /**
     * @dataProvider providerAND
     *
     * @param mixed $expectedResult
     */
    public function testAND($expectedResult, ...$args): void
    {
        $result = Logical\Operations::logicalAnd(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerAND(): array
    {
        return require 'tests/data/Calculation/Logical/AND.php';
    }
}

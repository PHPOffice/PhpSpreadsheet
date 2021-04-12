<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Logical;
use PHPUnit\Framework\TestCase;

class AndTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerAND
     *
     * @param mixed $expectedResult
     */
    public function testAND($expectedResult, ...$args): void
    {
        $result = Logical::logicalAnd(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerAND(): array
    {
        return require 'tests/data/Calculation/Logical/AND.php';
    }
}

<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\Logical;
use PHPUnit\Framework\TestCase;

class OrTest extends TestCase
{
    /**
     * @dataProvider providerOR
     *
     * @param mixed $expectedResult
     */
    public function testOR($expectedResult, ...$args): void
    {
        $result = Logical\Operations::logicalOr(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerOR(): array
    {
        return require 'tests/data/Calculation/Logical/OR.php';
    }
}

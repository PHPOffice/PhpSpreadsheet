<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\Logical;
use PHPUnit\Framework\TestCase;

class IfsTest extends TestCase
{
    /**
     * @dataProvider providerIFS
     *
     * @param mixed $expectedResult
     * @param mixed $args
     */
    public function testIFS($expectedResult, ...$args): void
    {
        $result = Logical\Conditional::IFS(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerIFS(): array
    {
        return require 'tests/data/Calculation/Logical/IFS.php';
    }
}

<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Logical;
use PHPUnit\Framework\TestCase;

class IfsTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerIFS
     *
     * @param mixed $expectedResult
     * @param mixed $args
     */
    public function testIFS($expectedResult, ...$args): void
    {
        $result = Logical::IFS(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerIFS(): array
    {
        return require 'tests/data/Calculation/Logical/IFS.php';
    }
}

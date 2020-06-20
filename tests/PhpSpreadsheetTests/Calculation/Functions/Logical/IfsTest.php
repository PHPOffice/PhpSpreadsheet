<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Logical;
use PHPUnit\Framework\TestCase;

class IfsTest extends TestCase
{
    public function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerIFS
     *
     * @param mixed $expectedResult
     */
    public function testIFS($expectedResult, ...$args)
    {
        $result = Logical::IFS(...$args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerIFS()
    {
        return require 'tests/data/Calculation/Logical/IFS.php';
    }
}

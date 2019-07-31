<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Logical;
use PHPUnit\Framework\TestCase;

class XorTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerXOR
     *
     * @param mixed $expectedResult
     */
    public function testXOR($expectedResult, ...$args)
    {
        $result = Logical::logicalXor(...$args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerXOR()
    {
        return require 'data/Calculation/Logical/XOR.php';
    }
}

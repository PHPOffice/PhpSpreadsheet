<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Logical;
use PHPUnit\Framework\TestCase;

class IfTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerIF
     *
     * @param mixed $expectedResult
     */
    public function testIF($expectedResult, ...$args)
    {
        $result = Logical::statementIf(...$args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerIF()
    {
        return require 'data/Calculation/Logical/IF.php';
    }
}

<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Logical;
use PHPUnit\Framework\TestCase;

class SwitchTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerSwitch
     *
     * @param mixed $expectedResult
     */
    public function testSWITCH($expectedResult, ...$args)
    {
        $result = Logical::statementSwitch(...$args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerSwitch()
    {
        return require 'data/Calculation/Logical/SWITCH.php';
    }
}

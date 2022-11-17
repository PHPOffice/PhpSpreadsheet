<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\Logical;
use PHPUnit\Framework\TestCase;

class SwitchTest extends TestCase
{
    /**
     * @dataProvider providerSwitch
     *
     * @param mixed $expectedResult
     */
    public function testSWITCH($expectedResult, ...$args): void
    {
        $result = Logical\Conditional::statementSwitch(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerSwitch(): array
    {
        return require 'tests/data/Calculation/Logical/SWITCH.php';
    }
}

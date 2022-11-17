<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\Logical;
use PHPUnit\Framework\TestCase;

class IfTest extends TestCase
{
    /**
     * @dataProvider providerIF
     *
     * @param mixed $expectedResult
     */
    public function testIF($expectedResult, ...$args): void
    {
        if (count($args) === 0) {
            $result = Logical\Conditional::statementIf();
        } elseif (count($args) === 1) {
            $result = Logical\Conditional::statementIf($args[0]);
        } elseif (count($args) === 2) {
            $result = Logical\Conditional::statementIf($args[0], $args[1]);
        } else {
            $result = Logical\Conditional::statementIf($args[0], $args[1], $args[2]);
        }
        self::assertEquals($expectedResult, $result);
    }

    public function providerIF(): array
    {
        return require 'tests/data/Calculation/Logical/IF.php';
    }
}

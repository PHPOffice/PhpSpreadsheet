<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PHPUnit\Framework\TestCase;

class ReplaceTest extends TestCase
{
    /**
     * @dataProvider providerREPLACE
     *
     * @param mixed $expectedResult
     */
    public function testREPLACE($expectedResult, ...$args): void
    {
        $result = TextData::REPLACE(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerREPLACE(): array
    {
        return require 'tests/data/Calculation/TextData/REPLACE.php';
    }
}

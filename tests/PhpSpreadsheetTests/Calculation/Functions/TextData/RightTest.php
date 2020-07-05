<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PHPUnit\Framework\TestCase;

class RightTest extends TestCase
{
    /**
     * @dataProvider providerRIGHT
     *
     * @param mixed $expectedResult
     */
    public function testRIGHT($expectedResult, ...$args): void
    {
        $result = TextData::RIGHT(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerRIGHT()
    {
        return require 'tests/data/Calculation/TextData/RIGHT.php';
    }
}

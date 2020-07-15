<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PHPUnit\Framework\TestCase;

class LowerTest extends TestCase
{
    /**
     * @dataProvider providerLOWER
     *
     * @param mixed $expectedResult
     * @param $value
     */
    public function testLOWER($expectedResult, $value): void
    {
        $result = TextData::LOWERCASE($value);
        self::assertEquals($expectedResult, $result);
    }

    public function providerLOWER()
    {
        return require 'tests/data/Calculation/TextData/LOWER.php';
    }
}

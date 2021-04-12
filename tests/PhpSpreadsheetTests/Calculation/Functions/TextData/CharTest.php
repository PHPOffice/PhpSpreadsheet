<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PHPUnit\Framework\TestCase;

class CharTest extends TestCase
{
    /**
     * @dataProvider providerCHAR
     *
     * @param mixed $expectedResult
     * @param mixed $character
     */
    public function testCHAR($expectedResult, $character): void
    {
        $result = TextData::CHARACTER($character);
        self::assertEquals($expectedResult, $result);
    }

    public function providerCHAR(): array
    {
        return require 'tests/data/Calculation/TextData/CHAR.php';
    }
}

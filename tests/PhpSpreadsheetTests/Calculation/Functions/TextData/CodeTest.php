<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PHPUnit\Framework\TestCase;

class CodeTest extends TestCase
{
    /**
     * @dataProvider providerCODE
     *
     * @param mixed $expectedResult
     * @param $character
     */
    public function testCODE($expectedResult, $character): void
    {
        $result = TextData::ASCIICODE($character);
        self::assertEquals($expectedResult, $result);
    }

    public function providerCODE()
    {
        return require 'tests/data/Calculation/TextData/CODE.php';
    }
}

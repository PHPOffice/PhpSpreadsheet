<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PHPUnit\Framework\TestCase;

class LeftTest extends TestCase
{
    /**
     * @dataProvider providerLEFT
     *
     * @param mixed $expectedResult
     */
    public function testLEFT($expectedResult, ...$args): void
    {
        $result = TextData::LEFT(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerLEFT()
    {
        return require 'tests/data/Calculation/TextData/LEFT.php';
    }
}

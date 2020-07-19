<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PHPUnit\Framework\TestCase;

class UpperTest extends TestCase
{
    /**
     * @dataProvider providerUPPER
     *
     * @param mixed $expectedResult
     * @param $value
     */
    public function testUPPER($expectedResult, $value): void
    {
        $result = TextData::UPPERCASE($value);
        self::assertEquals($expectedResult, $result);
    }

    public function providerUPPER()
    {
        return require 'tests/data/Calculation/TextData/UPPER.php';
    }
}

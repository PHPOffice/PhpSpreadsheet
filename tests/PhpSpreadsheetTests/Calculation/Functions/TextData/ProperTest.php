<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PHPUnit\Framework\TestCase;

class ProperTest extends TestCase
{
    /**
     * @dataProvider providerPROPER
     *
     * @param mixed $expectedResult
     * @param $value
     */
    public function testPROPER($expectedResult, $value): void
    {
        $result = TextData::PROPERCASE($value);
        self::assertEquals($expectedResult, $result);
    }

    public function providerPROPER()
    {
        return require 'tests/data/Calculation/TextData/PROPER.php';
    }
}

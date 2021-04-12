<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PHPUnit\Framework\TestCase;

class TTest extends TestCase
{
    /**
     * @dataProvider providerT
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testT($expectedResult, $value): void
    {
        $result = TextData::RETURNSTRING($value);
        self::assertEquals($expectedResult, $result);
    }

    public function providerT(): array
    {
        return require 'tests/data/Calculation/TextData/T.php';
    }
}

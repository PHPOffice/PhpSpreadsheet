<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PHPUnit\Framework\TestCase;

class LenTest extends TestCase
{
    /**
     * @dataProvider providerLEN
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testLEN($expectedResult, $value): void
    {
        $result = TextData::STRINGLENGTH($value);
        self::assertEquals($expectedResult, $result);
    }

    public function providerLEN(): array
    {
        return require 'tests/data/Calculation/TextData/LEN.php';
    }
}

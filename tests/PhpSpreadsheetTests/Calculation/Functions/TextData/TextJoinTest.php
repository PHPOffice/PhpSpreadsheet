<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PHPUnit\Framework\TestCase;

class TextJoinTest extends TestCase
{
    /**
     * @dataProvider providerTEXTJOIN
     *
     * @param mixed $expectedResult
     */
    public function testTEXTJOIN($expectedResult, array $args): void
    {
        $result = TextData::TEXTJOIN(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerTEXTJOIN(): array
    {
        return require 'tests/data/Calculation/TextData/TEXTJOIN.php';
    }
}

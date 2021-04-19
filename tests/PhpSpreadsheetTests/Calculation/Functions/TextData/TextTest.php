<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PHPUnit\Framework\TestCase;

class TextTest extends TestCase
{
    /**
     * @dataProvider providerTEXT
     *
     * @param mixed $expectedResult
     */
    public function testTEXT($expectedResult, ...$args): void
    {
        $result = TextData::TEXTFORMAT(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerTEXT(): array
    {
        return require 'tests/data/Calculation/TextData/TEXT.php';
    }
}

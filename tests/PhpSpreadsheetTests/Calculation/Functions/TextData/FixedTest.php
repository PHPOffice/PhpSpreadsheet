<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PHPUnit\Framework\TestCase;

class FixedTest extends TestCase
{
    /**
     * @dataProvider providerFIXED
     *
     * @param mixed $expectedResult
     */
    public function testFIXED($expectedResult, ...$args): void
    {
        $result = TextData::FIXEDFORMAT(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerFIXED(): array
    {
        return require 'tests/data/Calculation/TextData/FIXED.php';
    }
}

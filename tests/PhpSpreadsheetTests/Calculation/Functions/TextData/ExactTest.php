<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PHPUnit\Framework\TestCase;

class ExactTest extends TestCase
{
    /**
     * @dataProvider providerEXACT
     *
     * @param mixed $expectedResult
     * @param array $args
     */
    public function testEXACT($expectedResult, ...$args): void
    {
        $result = TextData::EXACT(...$args);
        self::assertSame($expectedResult, $result);
    }

    public function providerEXACT(): array
    {
        return require 'tests/data/Calculation/TextData/EXACT.php';
    }
}

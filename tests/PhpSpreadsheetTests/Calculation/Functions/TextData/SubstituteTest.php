<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PHPUnit\Framework\TestCase;

class SubstituteTest extends TestCase
{
    /**
     * @dataProvider providerSUBSTITUTE
     *
     * @param mixed $expectedResult
     */
    public function testSUBSTITUTE($expectedResult, ...$args): void
    {
        $result = TextData::SUBSTITUTE(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerSUBSTITUTE(): array
    {
        return require 'tests/data/Calculation/TextData/SUBSTITUTE.php';
    }
}

<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PHPUnit\Framework\TestCase;

class SearchTest extends TestCase
{
    /**
     * @dataProvider providerSEARCH
     *
     * @param mixed $expectedResult
     */
    public function testSEARCH($expectedResult, ...$args): void
    {
        $result = TextData::SEARCHINSENSITIVE(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerSEARCH(): array
    {
        return require 'tests/data/Calculation/TextData/SEARCH.php';
    }
}

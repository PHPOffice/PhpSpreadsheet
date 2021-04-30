<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PHPUnit\Framework\TestCase;

class FindTest extends TestCase
{
    /**
     * @dataProvider providerFIND
     *
     * @param mixed $expectedResult
     */
    public function testFIND($expectedResult, ...$args): void
    {
        $result = TextData::SEARCHSENSITIVE(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerFIND(): array
    {
        return require 'tests/data/Calculation/TextData/FIND.php';
    }
}

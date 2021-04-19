<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PHPUnit\Framework\TestCase;

class TrimTest extends TestCase
{
    /**
     * @dataProvider providerTRIM
     *
     * @param mixed $expectedResult
     * @param mixed $character
     */
    public function testTRIM($expectedResult, $character): void
    {
        $result = TextData::TRIMSPACES($character);
        self::assertEquals($expectedResult, $result);
    }

    public function providerTRIM(): array
    {
        return require 'tests/data/Calculation/TextData/TRIM.php';
    }
}

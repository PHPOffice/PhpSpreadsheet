<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PHPUnit\Framework\TestCase;

class CleanTest extends TestCase
{
    /**
     * @dataProvider providerCLEAN
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testCLEAN($expectedResult, $value): void
    {
        $result = TextData::TRIMNONPRINTABLE($value);
        self::assertEquals($expectedResult, $result);
    }

    public function providerCLEAN(): array
    {
        return require 'tests/data/Calculation/TextData/CLEAN.php';
    }
}

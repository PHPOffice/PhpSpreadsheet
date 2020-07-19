<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PHPUnit\Framework\TestCase;

class DollarTest extends TestCase
{
    /**
     * @dataProvider providerDOLLAR
     *
     * @param mixed $expectedResult
     */
    public function testDOLLAR($expectedResult, ...$args): void
    {
        $result = TextData::DOLLAR(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerDOLLAR()
    {
        return require 'tests/data/Calculation/TextData/DOLLAR.php';
    }
}

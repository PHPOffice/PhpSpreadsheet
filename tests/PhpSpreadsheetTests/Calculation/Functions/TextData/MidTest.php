<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PHPUnit\Framework\TestCase;

class MidTest extends TestCase
{
    /**
     * @dataProvider providerMID
     *
     * @param mixed $expectedResult
     */
    public function testMID($expectedResult, ...$args): void
    {
        $result = TextData::MID(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerMID()
    {
        return require 'tests/data/Calculation/TextData/MID.php';
    }
}

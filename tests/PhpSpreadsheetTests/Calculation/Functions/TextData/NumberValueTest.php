<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PHPUnit\Framework\TestCase;

class NumberValueTest extends TestCase
{
    /**
     * @dataProvider providerNUMBERVALUE
     *
     * @param mixed $expectedResult
     */
    public function testNUMBERVALUE($expectedResult, array $args): void
    {
        $result = TextData::NUMBERVALUE(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerNUMBERVALUE(): array
    {
        return require 'tests/data/Calculation/TextData/NUMBERVALUE.php';
    }
}

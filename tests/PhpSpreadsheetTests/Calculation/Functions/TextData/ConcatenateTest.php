<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PHPUnit\Framework\TestCase;

class ConcatenateTest extends TestCase
{
    /**
     * @dataProvider providerCONCATENATE
     *
     * @param mixed $expectedResult
     */
    public function testCONCATENATE($expectedResult, ...$args): void
    {
        $result = TextData::CONCATENATE(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerCONCATENATE(): array
    {
        return require 'tests/data/Calculation/TextData/CONCATENATE.php';
    }
}

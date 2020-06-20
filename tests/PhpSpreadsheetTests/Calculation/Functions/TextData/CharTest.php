<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PHPUnit\Framework\TestCase;

class CharTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        StringHelper::setDecimalSeparator('.');
        StringHelper::setThousandsSeparator(',');
        StringHelper::setCurrencyCode('$');
    }

    protected function tearDown(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        StringHelper::setDecimalSeparator('.');
        StringHelper::setThousandsSeparator(',');
        StringHelper::setCurrencyCode('$');
    }

    /**
     * @dataProvider providerCHAR
     *
     * @param mixed $expectedResult
     * @param $character
     */
    public function testCHAR($expectedResult, $character): void
    {
        $result = TextData::CHARACTER($character);
        self::assertEquals($expectedResult, $result);
    }

    public function providerCHAR()
    {
        return require 'tests/data/Calculation/TextData/CHAR.php';
    }
}

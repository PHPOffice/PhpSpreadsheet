<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PHPUnit\Framework\TestCase;

class CodeTest extends TestCase
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
     * @dataProvider providerCODE
     *
     * @param mixed $expectedResult
     * @param $character
     */
    public function testCODE($expectedResult, $character): void
    {
        $result = TextData::ASCIICODE($character);
        self::assertEquals($expectedResult, $result);
    }

    public function providerCODE()
    {
        return require 'tests/data/Calculation/TextData/CODE.php';
    }
}

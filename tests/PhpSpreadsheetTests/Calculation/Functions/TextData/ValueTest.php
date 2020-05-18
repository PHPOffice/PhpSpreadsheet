<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PHPUnit\Framework\TestCase;

class ValueTest extends TestCase
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
     * @dataProvider providerVALUE
     *
     * @param mixed $expectedResult
     * @param $value
     */
    public function testVALUE($expectedResult, $value): void
    {
        StringHelper::setDecimalSeparator('.');
        StringHelper::setThousandsSeparator(' ');
        StringHelper::setCurrencyCode('$');

        $result = TextData::VALUE($value);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerVALUE()
    {
        return require 'tests/data/Calculation/TextData/VALUE.php';
    }
}

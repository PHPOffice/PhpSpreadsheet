<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PHPUnit\Framework\TestCase;

class UpperTest extends TestCase
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
     * @dataProvider providerUPPER
     *
     * @param mixed $expectedResult
     * @param $value
     */
    public function testUPPER($expectedResult, $value)
    {
        $result = TextData::UPPERCASE($value);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerUPPER()
    {
        return require 'data/Calculation/TextData/UPPER.php';
    }
}

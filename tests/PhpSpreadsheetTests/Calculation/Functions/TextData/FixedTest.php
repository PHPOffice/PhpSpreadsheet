<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PHPUnit\Framework\TestCase;

class FixedTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        StringHelper::setDecimalSeparator('.');
        StringHelper::setThousandsSeparator(',');
        StringHelper::setCurrencyCode('$');
    }

    public function tearDown()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        StringHelper::setDecimalSeparator('.');
        StringHelper::setThousandsSeparator(',');
        StringHelper::setCurrencyCode('$');
    }

    /**
     * @dataProvider providerFIXED
     *
     * @param mixed $expectedResult
     */
    public function testFIXED($expectedResult, ...$args)
    {
        $result = TextData::FIXEDFORMAT(...$args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerFIXED()
    {
        return require 'data/Calculation/TextData/FIXED.php';
    }
}

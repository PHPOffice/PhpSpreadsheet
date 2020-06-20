<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PHPUnit\Framework\TestCase;

class TextTest extends TestCase
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
     * @dataProvider providerTEXT
     *
     * @param mixed $expectedResult
     */
    public function testTEXT($expectedResult, ...$args): void
    {
        //    Enforce decimal and thousands separator values to UK/US, and currency code to USD
        StringHelper::setDecimalSeparator('.');
        StringHelper::setThousandsSeparator(',');
        StringHelper::setCurrencyCode('$');

        $result = TextData::TEXTFORMAT(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerTEXT()
    {
        return require 'tests/data/Calculation/TextData/TEXT.php';
    }
}

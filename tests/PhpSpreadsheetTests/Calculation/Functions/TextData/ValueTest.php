<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PHPUnit\Framework\TestCase;

class ValueTest extends TestCase
{
    private $currencyCode;

    private $decimalSeparator;

    private $thousandsSeparator;

    protected function setUp(): void
    {
        $this->currencyCode = StringHelper::getCurrencyCode();
        $this->decimalSeparator = StringHelper::getDecimalSeparator();
        $this->thousandsSeparator = StringHelper::getThousandsSeparator();
    }

    protected function tearDown(): void
    {
        StringHelper::setCurrencyCode($this->currencyCode);
        StringHelper::setDecimalSeparator($this->decimalSeparator);
        StringHelper::setThousandsSeparator($this->thousandsSeparator);
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

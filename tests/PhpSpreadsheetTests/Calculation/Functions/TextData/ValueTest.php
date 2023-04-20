<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class ValueTest extends AllSetupTeardown
{
    /**
     * @var string
     */
    private $currencyCode;

    /**
     * @var string
     */
    private $decimalSeparator;

    /**
     * @var string
     */
    private $thousandsSeparator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->currencyCode = StringHelper::getCurrencyCode();
        $this->decimalSeparator = StringHelper::getDecimalSeparator();
        $this->thousandsSeparator = StringHelper::getThousandsSeparator();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        StringHelper::setCurrencyCode($this->currencyCode);
        StringHelper::setDecimalSeparator($this->decimalSeparator);
        StringHelper::setThousandsSeparator($this->thousandsSeparator);
    }

    /**
     * @dataProvider providerVALUE
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testVALUE($expectedResult, $value = 'omitted'): void
    {
        StringHelper::setDecimalSeparator('.');
        StringHelper::setThousandsSeparator(' ');
        StringHelper::setCurrencyCode('$');

        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($value === 'omitted') {
            $sheet->getCell('B1')->setValue('=VALUE()');
        } else {
            $this->setCell('A1', $value);
            $sheet->getCell('B1')->setValue('=VALUE(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public static function providerVALUE(): array
    {
        return require 'tests/data/Calculation/TextData/VALUE.php';
    }

    /**
     * @dataProvider providerValueArray
     */
    public function testValueArray(array $expectedResult, string $argument): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=VALUE({$argument})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerValueArray(): array
    {
        return [
            'row vector' => [[[44604, -1234.567]], '{"12-Feb-2022", "$ -1,234.567"}'],
        ];
    }
}

<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class ValueTest extends AllSetupTeardown
{
    protected function tearDown(): void
    {
        parent::tearDown();
        StringHelper::setCurrencyCode(null);
        StringHelper::setDecimalSeparator(null);
        StringHelper::setThousandsSeparator(null);
    }

    /**
     * @dataProvider providerVALUE
     */
    public function testVALUE(mixed $expectedResult, mixed $value = 'omitted'): void
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

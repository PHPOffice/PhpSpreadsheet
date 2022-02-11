<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class Days360Test extends AllSetupTeardown
{
    /**
     * @dataProvider providerDAYS360
     *
     * @param mixed $expectedResult
     */
    public function testDAYS360($expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('B1')->setValue('2000-02-29');
        $sheet->getCell('C1')->setValue('2000-03-31');
        $sheet->getCell('A1')->setValue("=DAYS360($formula)");
        self::assertSame($expectedResult, $sheet->getCell('A1')->getCalculatedValue());
    }

    public function providerDAYS360(): array
    {
        return require 'tests/data/Calculation/DateTime/DAYS360.php';
    }

    /**
     * @dataProvider providerDays360Array
     */
    public function testDays360Array(array $expectedResult, string $startDate, string $endDate, ?string $methods): void
    {
        $calculation = Calculation::getInstance();

        if ($methods === null) {
            $formula = "=DAYS360({$startDate}, {$endDate})";
        } else {
            $formula = "=DAYS360({$startDate}, {$endDate}, {$methods})";
        }
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerDays360Array(): array
    {
        return [
            'row vector #1' => [[[360, 199, -201]], '{"2022-01-01", "2022-06-12", "2023-07-22"}', '"2022-12-31"', null],
            'column vector #1' => [[[360], [358], [355]], '{"2022-01-01"; "2022-01-03"; "2022-01-06"}', '"2022-12-31"', null],
            'matrix #1' => [[[0, -9], [-224, -360]], '{"2022-01-01", "2022-01-10"; "2022-08-15", "2022-12-31"}', '"2021-12-31"', null],
            'column vector with methods' => [[[360, 359], [358, 357], [355, 354]], '{"2022-01-01"; "2022-01-03"; "2022-01-06"}', '"2022-12-31"', '{FALSE, TRUE}'],
        ];
    }
}

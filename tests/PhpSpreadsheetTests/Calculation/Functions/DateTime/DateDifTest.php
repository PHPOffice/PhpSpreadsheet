<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class DateDifTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDATEDIF
     *
     * @param mixed $expectedResult
     */
    public function testDATEDIF($expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('B1')->setValue('1954-11-23');
        $sheet->getCell('A1')->setValue("=DATEDIF($formula)");
        self::assertSame($expectedResult, $sheet->getCell('A1')->getCalculatedValue());
    }

    public function providerDATEDIF(): array
    {
        return require 'tests/data/Calculation/DateTime/DATEDIF.php';
    }

    /**
     * @dataProvider providerDateDifArray
     */
    public function testDateDifArray(array $expectedResult, string $startDate, string $endDate, ?string $methods): void
    {
        $calculation = Calculation::getInstance();

        if ($methods === null) {
            $formula = "=DATEDIF({$startDate}, {$endDate})";
        } else {
            $formula = "=DATEDIF({$startDate}, {$endDate}, {$methods})";
        }
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerDateDifArray(): array
    {
        return [
            'row vector #1' => [[[364, 202, '#NUM!']], '{"2022-01-01", "2022-06-12", "2023-07-22"}', '"2022-12-31"', null],
            'column vector #1' => [[[364], [362], [359]], '{"2022-01-01"; "2022-01-03"; "2022-01-06"}', '"2022-12-31"', null],
            'matrix #1' => [[[365, 266], [139, 1]], '{"2022-01-01", "2022-04-10"; "2022-08-15", "2022-12-31"}', '"2023-01-01"', null],
            'column vector with methods' => [[[364, 11], [242, 7], [173, 5]], '{"2022-01-01"; "2022-05-03"; "2022-07-11"}', '"2022-12-31"', '{"D", "M"}'],
        ];
    }
}

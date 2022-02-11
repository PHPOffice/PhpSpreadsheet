<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Month;

class EDateTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerEDATE
     *
     * @param mixed $expectedResult
     */
    public function testEDATE($expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue("=EDATE($formula)");
        $sheet->getCell('B1')->setValue('1954-11-23');
        self::assertEquals($expectedResult, $sheet->getCell('A1')->getCalculatedValue());
    }

    public function providerEDATE(): array
    {
        return require 'tests/data/Calculation/DateTime/EDATE.php';
    }

    public function testEDATEtoUnixTimestamp(): void
    {
        self::setUnixReturn();

        $result = Month::adjust('2012-1-26', -1);
        self::assertEquals(1324857600, $result);
        self::assertEqualsWithDelta(1324857600, $result, 1E-8);
    }

    public function testEDATEtoDateTimeObject(): void
    {
        self::setObjectReturn();

        $result = Month::adjust('2012-1-26', -1);
        //    Must return an object...
        self::assertIsObject($result);
        //    ... of the correct type
        self::assertTrue(is_a($result, 'DateTimeInterface'));
        //    ... with the correct value
        self::assertEquals($result->format('d-M-Y'), '26-Dec-2011');
    }

    /**
     * @dataProvider providerEDateArray
     */
    public function testEDateArray(array $expectedResult, string $dateValues, string $methods): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=EDATE({$dateValues}, {$methods})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerEDateArray(): array
    {
        return [
            'row vector #1' => [[[44593, 44632, 45337]], '{"2022-01-01", "2022-02-12", "2024-01-15"}', '1'],
            'column vector #1' => [[[44593], [44632], [45337]], '{"2022-01-01"; "2022-02-12"; "2024-01-15"}', '1'],
            'matrix #1' => [[[44593, 44632], [44652, 45343]], '{"2022-01-01", "2022-02-12"; "2022-03-01", "2024-01-21"}', '1'],
            'row vector #2' => [[[44573, 44604, 44632]], '"2022-02-12"', '{-1, 0, 1}'],
            'column vector #2' => [[[44573], [44604], [44632]], '"2022-02-12"', '{-1; 0; 1}'],
            'matrix #2' => [[[44573, 44604], [44632, 45334]], '"2022-02-12"', '{-1, 0; 1, 24}'],
        ];
    }
}

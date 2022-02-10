<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Month;

class EoMonthTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerEOMONTH
     *
     * @param mixed $expectedResult
     */
    public function testEOMONTH($expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue("=EOMONTH($formula)");
        $sheet->getCell('B1')->setValue('1954-11-23');
        self::assertEquals($expectedResult, $sheet->getCell('A1')->getCalculatedValue());
    }

    public function providerEOMONTH(): array
    {
        return require 'tests/data/Calculation/DateTime/EOMONTH.php';
    }

    public function testEOMONTHtoUnixTimestamp(): void
    {
        self::setUnixReturn();

        $result = Month::lastDay('2012-1-26', -1);
        self::assertEquals(1325289600, $result);
    }

    public function testEOMONTHtoDateTimeObject(): void
    {
        self::setObjectReturn();

        $result = Month::lastDay('2012-1-26', -1);
        //    Must return an object...
        self::assertIsObject($result);
        //    ... of the correct type
        self::assertTrue(is_a($result, 'DateTimeInterface'));
        //    ... with the correct value
        self::assertSame($result->format('d-M-Y'), '31-Dec-2011');
    }

    /**
     * @dataProvider providerEoMonthArray
     */
    public function testEoMonthArray(array $expectedResult, string $dateValues, string $methods): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=EOMONTH({$dateValues}, {$methods})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerEoMonthArray(): array
    {
        return [
            'row vector #1' => [[[44620, 44651, 45351]], '{"2022-01-01", "2022-02-12", "2024-01-15"}', '1'],
            'column vector #1' => [[[44620], [44651], [45351]], '{"2022-01-01"; "2022-02-12"; "2024-01-15"}', '1'],
            'matrix #1' => [[[44620, 44651], [44681, 45351]], '{"2022-01-01", "2022-02-12"; "2022-03-01", "2024-01-21"}', '1'],
            'row vector #2' => [[[44592, 44620, 44651]], '"2022-02-12"', '{-1, 0, 1}'],
            'column vector #2' => [[[44592], [44620], [44651]], '"2022-02-12"', '{-1; 0; 1}'],
            'matrix #2' => [[[44592, 44620], [44651, 45351]], '"2022-02-12"', '{-1, 0; 1, 24}'],
        ];
    }
}

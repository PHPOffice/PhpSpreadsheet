<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\TimeValue;

class TimeValueTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerTIMEVALUE
     *
     * @param mixed $expectedResult
     * @param mixed $timeValue
     */
    public function testTIMEVALUE($expectedResult, $timeValue): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('B1')->setValue('03:45:52');
        $sheet->getCell('A1')->setValue("=TIMEVALUE($timeValue)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerTIMEVALUE(): array
    {
        return require 'tests/data/Calculation/DateTime/TIMEVALUE.php';
    }

    public function testTIMEVALUEtoUnixTimestamp(): void
    {
        self::setUnixReturn();

        $result = TimeValue::fromString('7:30:20');
        self::assertEquals(23420, $result);
        self::assertEqualsWithDelta(23420, $result, 1E-8);
    }

    public function testTIMEVALUEtoDateTimeObject(): void
    {
        self::setObjectReturn();

        $result = TimeValue::fromString('7:30:20');
        //    Must return an object...
        self::assertIsObject($result);
        //    ... of the correct type
        self::assertTrue(is_a($result, 'DateTimeInterface'));
        //    ... with the correct value
        self::assertEquals($result->format('H:i:s'), '07:30:20');
    }

    /**
     * @dataProvider providerTimeValueArray
     */
    public function testTimeValueArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=TIMEVALUE({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerTimeValueArray(): array
    {
        return [
            'row vector' => [[[0.04309027777777, 0.5515625, 0.80579861111111]], '{"2022-02-09 01:02:03", "2022-02-09 13:14:15", "2022-02-09 19:20:21"}'],
            'column vector' => [[[0.04309027777777], [0.5515625], [0.80579861111111]], '{"2022-02-09 01:02:03"; "2022-02-09 13:14:15"; "2022-02-09 19:20:21"}'],
            'matrix' => [[[0.04309027777777, 0.5515625], [0.80579861111111, 0.99998842592592]], '{"2022-02-09 01:02:03", "2022-02-09 13:14:15"; "2022-02-09 19:20:21", "1999-12-31 23:59:59"}'],
        ];
    }
}

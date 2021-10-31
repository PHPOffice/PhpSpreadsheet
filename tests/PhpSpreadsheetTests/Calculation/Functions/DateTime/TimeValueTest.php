<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

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
}

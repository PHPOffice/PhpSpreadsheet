<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Time;

class TimeTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerTIME
     *
     * @param mixed $expectedResult
     */
    public function testTIME($expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('B1')->setValue('15');
        $sheet->getCell('B2')->setValue('32');
        $sheet->getCell('B3')->setValue('50');
        $sheet->getCell('A1')->setValue("=TIME($formula)");
        self::assertEqualsWithDelta($expectedResult, $sheet->getCell('A1')->getCalculatedValue(), 1E-8);
    }

    public function providerTIME(): array
    {
        return require 'tests/data/Calculation/DateTime/TIME.php';
    }

    public function testTIMEtoUnixTimestamp(): void
    {
        self::setUnixReturn();

        $result = Time::fromHMS(7, 30, 20);
        self::assertEqualsWithDelta(27020, $result, 1E-8);
    }

    public function testTIMEtoDateTimeObject(): void
    {
        self::setObjectReturn();

        $result = Time::fromHMS(7, 30, 20);
        //    Must return an object...
        self::assertIsObject($result);
        //    ... of the correct type
        self::assertTrue(is_a($result, 'DateTimeInterface'));
        //    ... with the correct value
        self::assertEquals($result->format('H:i:s'), '07:30:20');
    }

    public function testTIME1904(): void
    {
        self::setMac1904();
        $result = Time::fromHMS(0, 0, 0);
        self::assertEquals(0, $result);
    }

    public function testTIME1900(): void
    {
        $result = Time::fromHMS(0, 0, 0);
        self::assertEquals(0, $result);
    }
}

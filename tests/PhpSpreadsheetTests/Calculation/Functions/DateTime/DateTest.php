<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Date;

class DateTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDATE
     *
     * @param mixed $expectedResult
     */
    public function testDATE($expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('B1')->setValue('1954-11-23');
        $sheet->getCell('A1')->setValue("=DATE($formula)");
        self::assertEquals($expectedResult, $sheet->getCell('A1')->getCalculatedValue());
    }

    public function providerDATE(): array
    {
        return require 'tests/data/Calculation/DateTime/DATE.php';
    }

    public function testDATEtoUnixTimestamp(): void
    {
        self::setUnixReturn();

        $result = Date::fromYMD(2012, 1, 31); // 32-bit safe
        self::assertEquals(1327968000, $result);
    }

    public function testDATEtoDateTimeObject(): void
    {
        self::setObjectReturn();

        $result = Date::fromYMD(2012, 1, 31);
        //    Must return an object...
        self::assertIsObject($result);
        //    ... of the correct type
        self::assertTrue(is_a($result, 'DateTimeInterface'));
        //    ... with the correct value
        self::assertEquals($result->format('d-M-Y'), '31-Jan-2012');
    }

    public function testDATEwith1904Calendar(): void
    {
        self::setMac1904();

        $result = Date::fromYMD(1918, 11, 11);
        self::assertEquals($result, 5428);

        $result = Date::fromYMD(1901, 1, 31);
        self::assertEquals($result, '#NUM!');
    }
}

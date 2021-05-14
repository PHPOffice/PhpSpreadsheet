<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use DateTimeImmutable;
use DateTimeInterface;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\DateValue;

class DateValueTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDATEVALUE
     *
     * @param mixed $expectedResult
     */
    public function testDATEVALUE($expectedResult, string $dateValue): void
    {
        $this->getSheet()->getCell('B1')->setValue('1954-07-20');
        // Loop to avoid extraordinarily rare edge case where first calculation
        // and second do not take place on same day.
        $row = 0;
        do {
            ++$row;
            $dtStart = new DateTimeImmutable();
            $startDay = $dtStart->format('d');
            if (is_string($expectedResult)) {
                $replYMD = str_replace('Y', date('Y'), $expectedResult);
                if ($replYMD !== $expectedResult) {
                    $expectedResult = DateValue::fromString($replYMD);
                }
            }
            $this->getSheet()->getCell("A$row")->setValue("=DATEVALUE($dateValue)");
            $result = $this->getSheet()->getCell("A$row")->getCalculatedValue();
            $dtEnd = new DateTimeImmutable();
            $endDay = $dtEnd->format('d');
        } while ($startDay !== $endDay);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerDATEVALUE(): array
    {
        return require 'tests/data/Calculation/DateTime/DATEVALUE.php';
    }

    public function testDATEVALUEtoUnixTimestamp(): void
    {
        self::setUnixReturn();

        $result = DateValue::fromString('2012-1-31');
        self::assertEquals(1327968000, $result);
        self::assertEqualsWithDelta(1327968000, $result, 1E-8);
    }

    public function testDATEVALUEtoDateTimeObject(): void
    {
        self::setObjectReturn();

        $result = DateValue::fromString('2012-1-31');
        //    Must return an object...
        self::assertIsObject($result);
        //    ... of the correct type
        self::assertTrue(is_a($result, DateTimeInterface::class));
        //    ... with the correct value
        self::assertEquals($result->format('d-M-Y'), '31-Jan-2012');
    }

    public function testDATEVALUEwith1904Calendar(): void
    {
        self::setMac1904();
        self::assertEquals(5428, DateValue::fromString('1918-11-11'));
        self::assertEquals(0, DateValue::fromString('1904-01-01'));
        self::assertEquals('#VALUE!', DateValue::fromString('1903-12-31'));
        self::assertEquals('#VALUE!', DateValue::fromString('1900-02-29'));
    }
}

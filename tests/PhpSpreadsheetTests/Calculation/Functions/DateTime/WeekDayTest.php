<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Week;

class WeekDayTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerWEEKDAY
     *
     * @param mixed $expectedResult
     */
    public function testWEEKDAY($expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('B1')->setValue('1954-11-23');
        $sheet->getCell('A1')->setValue("=WEEKDAY($formula)");
        self::assertSame($expectedResult, $sheet->getCell('A1')->getCalculatedValue());
    }

    public function providerWEEKDAY(): array
    {
        return require 'tests/data/Calculation/DateTime/WEEKDAY.php';
    }

    public function testWEEKDAYwith1904Calendar(): void
    {
        self::setMac1904();
        self::assertEquals(7, Week::day('1904-01-02'));
        self::assertEquals(6, Week::day('1904-01-01'));
        self::assertEquals(6, Week::day(null));
    }
}

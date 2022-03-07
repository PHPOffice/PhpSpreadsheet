<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use DateTime;
use DateTimeImmutable;
use Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Days;

class DaysTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDAYS
     *
     * @param mixed $expectedResult
     */
    public function testDAYS($expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('B1')->setValue('1954-11-23');
        $sheet->getCell('C1')->setValue('1954-11-30');
        $sheet->getCell('A1')->setValue("=DAYS($formula)");
        self::assertSame($expectedResult, $sheet->getCell('A1')->getCalculatedValue());
    }

    public function providerDAYS(): array
    {
        return require 'tests/data/Calculation/DateTime/DAYS.php';
    }

    public function testObject(): void
    {
        $obj1 = new DateTime('2000-3-31');
        $obj2 = new DateTimeImmutable('2000-2-29');
        self::assertSame(31, Days::between($obj1, $obj2));
    }

    public function testNonDateObject(): void
    {
        $obj1 = new Exception();
        $obj2 = new DateTimeImmutable('2000-2-29');
        self::assertSame('#VALUE!', Days::between($obj1, $obj2));
    }

    /**
     * @dataProvider providerDaysArray
     */
    public function testDaysArray(array $expectedResult, string $startDate, string $endDate): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=DAYS({$startDate}, {$endDate})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerDaysArray(): array
    {
        return [
            'row vector #1' => [[[-364, -202, 203]], '{"2022-01-01", "2022-06-12", "2023-07-22"}', '"2022-12-31"'],
            'column vector #1' => [[[-364], [-362], [-359]], '{"2022-01-01"; "2022-01-03"; "2022-01-06"}', '"2022-12-31"'],
            'matrix #1' => [[[1, 10], [227, 365]], '{"2022-01-01", "2022-01-10"; "2022-08-15", "2022-12-31"}', '"2021-12-31"'],
        ];
    }
}

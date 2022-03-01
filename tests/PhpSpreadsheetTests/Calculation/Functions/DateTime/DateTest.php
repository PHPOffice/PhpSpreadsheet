<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Date;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;

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

    /**
     * @dataProvider providerDateArray
     */
    public function testDateArray(array $expectedResult, string $year, string $month, string $day): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=DATE({$year}, {$month}, {$day})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerDateArray(): array
    {
        return [
            'row vector year' => [[[44197, 44562, 44927]], '{2021,2022,2023}', '1', '1'],
            'column vector year' => [[[44197], [44562], [44927]], '{2021;2022;2023}', '1', '1'],
            'matrix year' => [[[43831.00, 44197], [44562, 44927]], '{2020,2021;2022,2023}', '1', '1'],
            'row vector month' => [[[44562, 44652, 44743, 44835]], '2022', '{1, 4, 7, 10}', '1'],
            'column vector month' => [[[44562], [44652], [44743], [44835]], '2022', '{1; 4; 7; 10}', '1'],
            'matrix month' => [[[44562, 44652], [44743, 44835]], '2022', '{1, 4; 7, 10}', '1'],
            'row vector day' => [[[44561, 44562]], '2022', '1', '{0,1}'],
            'column vector day' => [[[44561], [44562]], '2022', '1', '{0;1}'],
            'vectors year and month' => [
                [
                    [44197, 44287, 44378, 44470],
                    [44562, 44652, 44743, 44835],
                    [44927, 45017, 45108, 45200],
                ],
                '{2021;2022;2023}',
                '{1, 4, 7, 10}',
                '1',
            ],
            'vectors year and day' => [
                [
                    [44196, 44197],
                    [44561, 44562],
                    [44926, 44927],
                ],
                '{2021;2022;2023}',
                '1',
                '{0,1}',
            ],
            'vectors month and day' => [
                [
                    [44561, 44562],
                    [44651, 44652],
                    [44742, 44743],
                    [44834, 44835],
                ],
                '2022',
                '{1; 4; 7; 10}',
                '{0,1}',
            ],
            'matrices year and month' => [
                [
                    [43831, 44287],
                    [44743, 45200],
                ],
                '{2020, 2021; 2022, 2023}',
                '{1, 4; 7, 10}',
                '1',
            ],
        ];
    }

    /**
     * @dataProvider providerDateArrayException
     */
    public function testDateArrayException(string $year, string $month, string $day): void
    {
        $calculation = Calculation::getInstance();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Formulae with more than two array arguments are not supported');

        $formula = "=DATE({$year}, {$month}, {$day})";
        $calculation->_calculateFormulaValue($formula);
    }

    public function providerDateArrayException(): array
    {
        return [
            'matrix arguments with 3 array values' => [
                '{2020, 2021; 2022, 2023}',
                '{1, 4; 7, 10}',
                '{0,1}',
            ],
        ];
    }
}

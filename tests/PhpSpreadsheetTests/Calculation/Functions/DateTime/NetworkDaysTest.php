<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class NetworkDaysTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerNETWORKDAYS
     *
     * @param mixed $expectedResult
     * @param mixed $arg1
     * @param mixed $arg2
     */
    public function testNETWORKDAYS($expectedResult, $arg1 = 'omitted', $arg2 = 'omitted', ?array $arg3 = null): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($arg1 !== null) {
            $sheet->getCell('A1')->setValue($arg1);
        }
        if ($arg2 !== null) {
            $sheet->getCell('A2')->setValue($arg2);
        }
        $dateArray = [];
        if (is_array($arg3)) {
            if (array_key_exists(0, $arg3) && is_array($arg3[0])) {
                $dateArray = $arg3[0];
            } else {
                $dateArray = $arg3;
            }
        }
        $dateIndex = 0;
        foreach ($dateArray as $date) {
            ++$dateIndex;
            $sheet->getCell("C$dateIndex")->setValue($date);
        }
        $arrayArg = $dateIndex ? ", C1:C$dateIndex" : '';
        if ($arg1 === 'omitted') {
            $sheet->getCell('B1')->setValue('=NETWORKDAYS()');
        } elseif ($arg2 === 'omitted') {
            $sheet->getCell('B1')->setValue('=NETWORKDAYS(A1)');
        } else {
            $sheet->getCell('B1')->setValue("=NETWORKDAYS(A1, A2$arrayArg)");
        }
        self::assertEquals($expectedResult, $sheet->getCell('B1')->getCalculatedValue());
    }

    public function providerNETWORKDAYS(): array
    {
        return require 'tests/data/Calculation/DateTime/NETWORKDAYS.php';
    }

    /**
     * @dataProvider providerNetWorkDaysArray
     */
    public function testNetWorkDaysArray(array $expectedResult, string $startDate, string $endDays, ?string $holidays): void
    {
        $calculation = Calculation::getInstance();

        if ($holidays === null) {
            $formula = "=NETWORKDAYS({$startDate}, {$endDays})";
        } else {
            $formula = "=NETWORKDAYS({$startDate}, {$endDays}, {$holidays})";
        }
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerNetWorkDaysArray(): array
    {
        return [
            'row vector #1' => [[[234, 233, 232]], '{"2022-02-01", "2022-02-02", "2022-02-03"}', '"2022-12-25"', null],
            'column vector #1' => [[[234], [233], [232]], '{"2022-02-01"; "2022-02-02"; "2022-02-03"}', '"2022-12-25"', null],
            'matrix #1' => [[[234, 233], [232, 231]], '{"2022-02-01", "2022-02-02"; "2022-02-03", "2022-02-04"}', '"2022-12-25"', null],
            'row vector #2' => [[[234, -27]], '"2022-02-01"', '{"2022-12-25", "2021-12-25"}', null],
            'column vector #2' => [[[234], [-27]], '"2022-02-01"', '{"2022-12-25"; "2021-12-25"}', null],
            'row vector with Holiday' => [[[233, -27]], '"2022-02-01"', '{"2022-12-25", "2021-12-25"}', '{"2022-02-02"}'],
            'row vector with Holidays' => [[[232, -27]], '"2022-02-01"', '{"2022-12-25", "2021-12-25"}', '{"2022-02-02", "2022-02-03"}'],
        ];
    }
}

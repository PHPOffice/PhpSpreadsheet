<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

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
}

<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class SumIfTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSUMIF
     *
     * @param mixed $expectedResult
     * @param mixed $condition
     */
    public function testSUMIF2($expectedResult, array $array1, $condition, ?array $array2 = null): void
    {
        $this->mightHaveException($expectedResult);
        if ($expectedResult === 'incomplete') {
            self::markTestIncomplete('Raises formula error - researching solution');
        }
        $sheet = $this->getSheet();
        $sheet->fromArray($array1, null, 'A1', true);
        $maxARow = count($array1);
        $firstArg = "A1:A$maxARow";
        $this->setCell('B1', $condition);
        $secondArg = 'B1';
        if (empty($array2)) {
            $sheet->getCell('D1')->setValue("=SUMIF($firstArg, $secondArg)");
        } else {
            $sheet->fromArray($array2, null, 'C1', true);
            $maxCRow = count($array2);
            $thirdArg = "C1:C$maxCRow";
            $sheet->getCell('D1')->setValue("=SUMIF($firstArg, $secondArg, $thirdArg)");
        }
        $result = $sheet->getCell('D1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public static function providerSUMIF(): array
    {
        return require 'tests/data/Calculation/MathTrig/SUMIF.php';
    }
}

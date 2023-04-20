<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class SumSqTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSUMSQ
     *
     * @param mixed $expectedResult
     */
    public function testSUMSQ($expectedResult, ...$args): void
    {
        $this->mightHaveException($expectedResult);
        $maxRow = 0;
        $funcArg = '';
        $sheet = $this->getSheet();
        foreach ($args as $arg) {
            ++$maxRow;
            $funcArg = "A1:A$maxRow";
            if ($arg !== null) {
                $sheet->getCell("A$maxRow")->setValue($arg);
            }
        }
        $sheet->getCell('B1')->setValue("=SUMSQ($funcArg)");
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public static function providerSUMSQ(): array
    {
        return require 'tests/data/Calculation/MathTrig/SUMSQ.php';
    }
}

<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class GcdTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerGCD
     *
     * @param mixed $expectedResult
     */
    public function testGCD($expectedResult, ...$args): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $row = 0;
        foreach ($args as $arg) {
            ++$row;
            if ($arg !== null) {
                $sheet->getCell("A$row")->setValue($arg);
            }
        }
        if ($row < 1) {
            $sheet->getCell('B1')->setValue('=GCD()');
        } else {
            $sheet->getCell('B1')->setValue("=GCD(A1:A$row)");
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerGCD(): array
    {
        return require 'tests/data/Calculation/MathTrig/GCD.php';
    }
}

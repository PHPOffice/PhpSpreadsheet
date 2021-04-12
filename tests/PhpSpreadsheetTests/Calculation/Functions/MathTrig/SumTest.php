<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class SumTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSUM
     *
     * @param mixed $expectedResult
     */
    public function testSUM($expectedResult, ...$args): void
    {
        $sheet = $this->sheet;
        $row = 0;
        foreach ($args as $arg) {
            ++$row;
            $sheet->getCell("A$row")->setValue($arg);
        }
        $sheet->getCell('B1')->setValue("=SUM(A1:A$row)");
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerSUM(): array
    {
        return require 'tests/data/Calculation/MathTrig/SUM.php';
    }
}

<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

class ValueToTextTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerVALUE
     *
     * @param mixed $expectedResult
     * @param mixed $value
     * @param mixed $format
     */
    public function testVALUETOTEXT($expectedResult, $value, $format): void
    {
        $sheet = $this->getSheet();
        $this->setCell('A1', $value);
        $sheet->getCell('B1')->setValue("=VALUETOTEXT(A1, {$format})");

        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public function providerVALUE(): array
    {
        return require 'tests/data/Calculation/TextData/VALUETOTEXT.php';
    }
}

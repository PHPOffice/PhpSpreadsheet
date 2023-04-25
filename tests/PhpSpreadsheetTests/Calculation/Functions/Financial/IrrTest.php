<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class IrrTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerIRR
     *
     * @param mixed $expectedResult
     * @param mixed $values
     */
    public function testIRR($expectedResult, $values = null): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $formula = '=IRR(';
        if ($values !== null) {
            if (is_array($values)) {
                $row = 0;
                foreach ($values as $value) {
                    if (is_array($value)) {
                        foreach ($value as $arrayValue) {
                            ++$row;
                            $sheet->getCell("A$row")->setValue($arrayValue);
                        }
                    } else {
                        ++$row;
                        $sheet->getCell("A$row")->setValue($value);
                    }
                }
                $formula .= "A1:A$row";
            } else {
                $sheet->getCell('A1')->setValue($values);
                $formula .= 'A1';
            }
        }
        $formula .= ')';
        $sheet->getCell('D1')->setValue($formula);
        $result = $sheet->getCell('D1')->getCalculatedValue();
        $this->adjustResult($result, $expectedResult);

        self::assertEqualsWithDelta($expectedResult, $result, 0.1E-8);
    }

    public static function providerIRR(): array
    {
        return require 'tests/data/Calculation/Financial/IRR.php';
    }
}

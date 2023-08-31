<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class XirrTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerXIRR
     *
     * @param mixed $expectedResult
     * @param mixed $values
     * @param mixed $dates
     * @param mixed $guess
     */
    public function testXIRR($expectedResult, $values = null, $dates = null, $guess = null): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $formula = '=XIRR(';
        if ($values !== null) {
            if (is_array($values)) {
                $row = 0;
                foreach ($values as $value) {
                    ++$row;
                    $sheet->getCell("A$row")->setValue($value);
                }
                $formula .= "A1:A$row";
            } else {
                $sheet->getCell('A1')->setValue($values);
                $formula .= 'A1';
            }
            if ($dates !== null) {
                if (is_array($dates)) {
                    $row = 0;
                    foreach ($dates as $date) {
                        ++$row;
                        $sheet->getCell("B$row")->setValue($date);
                    }
                    $formula .= ",B1:B$row";
                } else {
                    $sheet->getCell('B1')->setValue($dates);
                    $formula .= ',B1';
                }
                if ($guess !== null) {
                    if ($guess !== 'C1') {
                        $sheet->getCell('C1')->setValue($guess);
                    }
                    $formula .= ', C1';
                }
            }
        }
        $formula .= ')';
        $sheet->getCell('D1')->setValue($formula);
        $result = $sheet->getCell('D1')->getCalculatedValue();
        $this->adjustResult($result, $expectedResult);

        self::assertEqualsWithDelta($expectedResult, $result, 0.1E-7);
    }

    public static function providerXIRR(): array
    {
        return require 'tests/data/Calculation/Financial/XIRR.php';
    }
}

<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class MirrTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerMIRR
     *
     * @param mixed $expectedResult
     * @param mixed $values
     * @param mixed $financeRate
     * @param mixed $reinvestRate
     */
    public function testMIRR($expectedResult, $values, $financeRate = null, $reinvestRate = null): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $formula = '=MIRR(';
        if ($values !== null) {
            if (is_array($values)) {
                $values = Functions::flattenArray($values);
                $row = 0;
                foreach ($values as $value) {
                    ++$row;
                    $this->setCell("A$row", $value);
                }
                $formula .= "A1:A$row";
            } else {
                $this->setCell('A1', $values);
                $formula .= 'A1';
            }
            if ($financeRate !== null) {
                $this->setCell('B1', $financeRate);
                $formula .= ',B1';
                if ($reinvestRate !== null) {
                    $this->setCell('B2', $reinvestRate);
                    $formula .= ',B2';
                }
            }
        }
        $formula .= ')';
        $sheet->getCell('D1')->setValue($formula);
        $result = $sheet->getCell('D1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1.0E-8);
    }

    public static function providerMIRR(): array
    {
        return require 'tests/data/Calculation/Financial/MIRR.php';
    }
}

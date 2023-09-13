<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class XNpvTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerXNPV
     */
    public function testXNPV(mixed $expectedResult, mixed $rate = null, mixed $values = null, mixed $dates = null): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $formula = '=XNPV(';
        if ($rate !== null) {
            $this->setCell('C1', $rate);
            $formula .= 'C1,';
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
                }
            }
        }
        $formula .= ')';
        $sheet->getCell('D1')->setValue($formula);
        $result = $sheet->getCell('D1')->getCalculatedValue();
        if (is_numeric($result) && is_numeric($expectedResult)) {
            if ($expectedResult != 0) {
                $frac = $result / $expectedResult;
                if ($frac > 0.999999 && $frac < 1.000001) {
                    $result = $expectedResult;
                }
            } elseif (abs((float) $result) < 1E-4) {
                $result = 0;
            }
        }
        self::assertEquals($expectedResult, $result);
    }

    public static function providerXNPV(): array
    {
        return require 'tests/data/Calculation/Financial/XNPV.php';
    }
}

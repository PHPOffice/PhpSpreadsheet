<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class RandBetweenTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerRANDBETWEEN
     *
     * @param mixed $expectedResult
     * @param mixed $min
     * @param mixed $max
     */
    public function testRANDBETWEEN($expectedResult, $min = 'omitted', $max = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $lower = (int) $min;
        $upper = (int) $max;
        if ($min !== null) {
            $sheet->getCell('A1')->setValue($min);
        }
        if ($max !== null) {
            $sheet->getCell('A2')->setValue($max);
        }
        if ($min === 'omitted') {
            $sheet->getCell('B1')->setValue('=RANDBETWEEN()');
        } elseif ($max === 'omitted') {
            $sheet->getCell('B1')->setValue('=RANDBETWEEN(A1)');
        } else {
            $sheet->getCell('B1')->setValue('=RANDBETWEEN(A1,A2)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        if (is_numeric($expectedResult)) {
            self::assertGreaterThanOrEqual($lower, $result);
            self::assertLessThanOrEqual($upper, $result);
        } else {
            self::assertSame($expectedResult, $result);
        }
    }

    public function providerRANDBETWEEN(): array
    {
        return require 'tests/data/Calculation/MathTrig/RANDBETWEEN.php';
    }
}

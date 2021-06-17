<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class RadiansTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerRADIANS
     *
     * @param mixed $expectedResult
     * @param mixed $number
     */
    public function testRADIANS($expectedResult, $number = 'omitted'): void
    {
        $sheet = $this->getSheet();
        $this->mightHaveException($expectedResult);
        $this->setCell('A1', $number);
        if ($number === 'omitted') {
            $sheet->getCell('B1')->setValue('=RADIANS()');
        } else {
            $sheet->getCell('B1')->setValue('=RADIANS(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-9);
    }

    public function providerRADIANS(): array
    {
        return require 'tests/data/Calculation/MathTrig/RADIANS.php';
    }
}

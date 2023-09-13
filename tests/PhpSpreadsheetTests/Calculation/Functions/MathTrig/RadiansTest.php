<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class RadiansTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerRADIANS
     */
    public function testRADIANS(mixed $expectedResult, mixed $number = 'omitted'): void
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

    public static function providerRADIANS(): array
    {
        return require 'tests/data/Calculation/MathTrig/RADIANS.php';
    }

    /**
     * @dataProvider providerRadiansArray
     */
    public function testRadiansArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=RADIANS({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerRadiansArray(): array
    {
        return [
            'row vector' => [[[1.48352986419518, 3.92699081698724, -0.26179938779915]], '{85, 225, -15}'],
            'column vector' => [[[1.48352986419518], [3.92699081698724], [-0.26179938779915]], '{85; 225; -15}'],
            'matrix' => [[[1.48352986419518, 3.92699081698724], [7.85398163397448, -0.26179938779915]], '{85, 225; 450, -15}'],
        ];
    }
}

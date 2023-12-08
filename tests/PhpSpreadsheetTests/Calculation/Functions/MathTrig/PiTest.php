<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class PiTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerPI
     */
    public function testPI(mixed $expectedResult, mixed $number = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($number !== null) {
            $sheet->getCell('A1')->setValue($number);
        }
        if ($number === 'omitted') {
            $sheet->getCell('B1')->setValue('=PI()');
        } else {
            $sheet->getCell('B1')->setValue('=PI(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public static function providerPI(): array
    {
        return require 'tests/data/Calculation/MathTrig/PI.php';
    }
}

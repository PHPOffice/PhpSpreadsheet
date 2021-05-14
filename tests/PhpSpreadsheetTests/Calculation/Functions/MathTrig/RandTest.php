<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class RandTest extends AllSetupTeardown
{
    public function testRand(): void
    {
        $sheet = $this->getSheet();
        $sheet->getCell('B1')->setValue('=RAND()');
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertGreaterThanOrEqual(0, $result);
        self::assertLessThanOrEqual(1, $result);
    }

    public function testRandException(): void
    {
        $this->mightHaveException('exception');
        $sheet = $this->getSheet();
        $sheet->getCell('B1')->setValue('=RAND(A1)');
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals(0, $result);
    }
}

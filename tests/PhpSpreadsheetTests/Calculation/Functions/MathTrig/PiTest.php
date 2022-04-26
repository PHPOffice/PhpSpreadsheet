<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class PiTest extends AllSetupTeardown
{
    public function testPI(): void
    {
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('=PI()');
        self::assertEquals(M_PI, $sheet->getCell('A1')->getCalculatedValue());
    }
}

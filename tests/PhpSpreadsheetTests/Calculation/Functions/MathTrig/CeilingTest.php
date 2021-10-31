<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class CeilingTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCEILING
     *
     * @param mixed $expectedResult
     * @param string $formula
     */
    public function testCEILING($expectedResult, $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 1.3);
        $sheet->setCellValue('A3', 2.7);
        $sheet->setCellValue('A4', -3.8);
        $sheet->setCellValue('A5', -5.2);
        $sheet->getCell('A1')->setValue("=CEILING($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerCEILING(): array
    {
        return require 'tests/data/Calculation/MathTrig/CEILING.php';
    }

    public function testCEILINGGnumeric1Arg(): void
    {
        self::setGnumeric();
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('=CEILING(5.1)');
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta(6, $result, 1E-12);
    }

    public function testCELINGOpenOffice1Arg(): void
    {
        self::setOpenOffice();
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('=CEILING(5.1)');
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta(6, $result, 1E-12);
    }

    public function testCEILINGExcel1Arg(): void
    {
        $this->mightHaveException('exception');
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('=CEILING(5.1)');
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta(6, $result, 1E-12);
    }
}

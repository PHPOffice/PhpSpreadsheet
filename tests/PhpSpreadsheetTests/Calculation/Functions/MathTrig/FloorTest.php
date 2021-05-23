<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class FloorTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerFLOOR
     *
     * @param mixed $expectedResult
     * @param string $formula
     */
    public function testFLOOR($expectedResult, $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 1.3);
        $sheet->setCellValue('A3', 2.7);
        $sheet->setCellValue('A4', -3.8);
        $sheet->setCellValue('A5', -5.2);
        $sheet->getCell('A1')->setValue("=FLOOR($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerFLOOR(): array
    {
        return require 'tests/data/Calculation/MathTrig/FLOOR.php';
    }

    public function testFLOORGnumeric1Arg(): void
    {
        self::setGnumeric();
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('=FLOOR(5.1)');
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta(5, $result, 1E-12);
    }

    public function testFLOOROpenOffice1Arg(): void
    {
        self::setOpenOffice();
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('=FLOOR(5.1)');
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta(5, $result, 1E-12);
    }

    public function testFLOORExcel1Arg(): void
    {
        $this->mightHaveException('exception');
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('=FLOOR(5.1)');
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta(5, $result, 1E-12);
    }
}

<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class FloorTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerFLOOR
     */
    public function testFLOOR(mixed $expectedResult, string $formula): void
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

    public static function providerFLOOR(): array
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

    /**
     * @dataProvider providerFloorArray
     */
    public function testFloorArray(array $expectedResult, string $argument1, string $argument2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=FLOOR({$argument1}, {$argument2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerFloorArray(): array
    {
        return [
            'matrix' => [[[3.14, 3.14], [3.14155, 3.141592]], '3.1415926536', '{0.01, 0.002; 0.00005, 0.000002}'],
        ];
    }
}

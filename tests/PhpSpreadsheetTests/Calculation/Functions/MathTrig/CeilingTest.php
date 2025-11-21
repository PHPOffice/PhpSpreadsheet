<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PHPUnit\Framework\Attributes\DataProvider;

class CeilingTest extends AllSetupTeardown
{
    #[DataProvider('providerCEILING')]
    public function testCEILING(mixed $expectedResult, string $formula): void
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

    public static function providerCEILING(): array
    {
        return require 'tests/data/Calculation/MathTrig/CEILING.php';
    }

    public function testCEILINGGnumericClashingSigns(): void
    {
        self::setGnumeric();
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('=CEILING(17,8)');
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta(24, $result, 1E-12);
        $sheet->getCell('A2')->setValue('=CEILING(-17,-8)');
        $result = $sheet->getCell('A2')->getCalculatedValue();
        self::assertEqualsWithDelta(-24, $result, 1E-12);
        $sheet->getCell('A3')->setValue('=CEILING(17,-8)');
        $result = $sheet->getCell('A3')->getCalculatedValue();
        self::assertSame('#NUM!', $result);
        $sheet->getCell('A4')->setValue('=CEILING(-17,8)');
        $result = $sheet->getCell('A4')->getCalculatedValue();
        self::assertSame('#NUM!', $result);
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

    #[DataProvider('providerCeilingArray')]
    public function testCeilingArray(array $expectedResult, string $argument1, string $argument2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=CEILING({$argument1}, {$argument2})";
        $result = $calculation->calculateFormula($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerCeilingArray(): array
    {
        return [
            'matrix' => [[[3.15, 3.142], [3.1416, 3.141594]], '3.1415926536', '{0.01, 0.002; 0.00005, 0.000002}'],
        ];
    }
}

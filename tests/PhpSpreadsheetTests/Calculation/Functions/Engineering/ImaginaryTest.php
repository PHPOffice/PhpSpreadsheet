<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\Complex;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\Attributes\DataProvider;

class ImaginaryTest extends AllSetupTeardown
{
    const COMPLEX_PRECISION = 1E-12;

    #[DataProvider('providerIMAGINARY')]
    public function testDirectCallToIMAGINARY(float|int|string $expectedResult, float|int|string $arg): void
    {
        $result = Complex::IMAGINARY((string) $arg);
        self::assertEqualsWithDelta($expectedResult, $result, self::COMPLEX_PRECISION);
    }

    #[DataProvider('providerIMAGINARY')]
    public function testIMAGINARYAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=IMAGINARY({$arguments})";

        $result = $calculation->calculateFormula($formula);
        self::assertEqualsWithDelta($expectedResult, $result, self::COMPLEX_PRECISION);
    }

    #[DataProvider('providerIMAGINARY')]
    public function testIMAGINARYInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $worksheet = $this->getSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMAGINARY({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, self::COMPLEX_PRECISION);
    }

    public static function providerIMAGINARY(): array
    {
        return require 'tests/data/Calculation/Engineering/IMAGINARY.php';
    }

    #[DataProvider('providerUnhappyIMAGINARY')]
    public function testIMAGINARYUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $worksheet = $this->getSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMAGINARY({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
    }

    public static function providerUnhappyIMAGINARY(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for IMAGINARY() function'],
        ];
    }

    /** @param mixed[] $expectedResult */
    #[DataProvider('providerImaginaryArray')]
    public function testImaginaryArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMAGINARY({$complex})";
        $result = $calculation->calculateFormula($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerImaginaryArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [-2.5, -2.5, -2.5],
                    [-1.0, -1.0, -1.0],
                    [1.0, 1.0, 1.0],
                    [2.5, 2.5, 2.5],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}

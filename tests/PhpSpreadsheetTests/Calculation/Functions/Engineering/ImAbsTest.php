<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\Attributes\DataProvider;

class ImAbsTest extends AllSetupTeardown
{
    const COMPLEX_PRECISION = 1E-12;

    #[DataProvider('providerIMABS')]
    public function testDirectCallToIMABS(float|int|string $expectedResult, string $arg): void
    {
        $result = ComplexFunctions::IMABS($arg);
        self::assertEqualsWithDelta($expectedResult, $result, self::COMPLEX_PRECISION);
    }

    #[DataProvider('providerIMABS')]
    public function testIMABSAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=IMABS({$arguments})";

        $result = $calculation->calculateFormula($formula);
        self::assertEqualsWithDelta($expectedResult, $result, self::COMPLEX_PRECISION);
    }

    #[DataProvider('providerIMABS')]
    public function testIMABSInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $worksheet = $this->getSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMABS({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, self::COMPLEX_PRECISION);
    }

    public static function providerIMABS(): array
    {
        return require 'tests/data/Calculation/Engineering/IMABS.php';
    }

    #[DataProvider('providerUnhappyIMABS')]
    public function testIMABSUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $worksheet = $this->getSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMABS({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
    }

    public static function providerUnhappyIMABS(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for IMABS() function'],
        ];
    }

    #[DataProvider('providerImAbsArray')]
    public function testImAbsArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMABS({$complex})";
        $result = $calculation->calculateFormula($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerImAbsArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [2.692582403567252, 2.5, 2.692582403567252],
                    [1.4142135623730951, 1.0, 1.4142135623730951],
                    [1.4142135623730951, 1.0, 1.4142135623730951],
                    [2.692582403567252, 2.5, 2.692582403567252],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}

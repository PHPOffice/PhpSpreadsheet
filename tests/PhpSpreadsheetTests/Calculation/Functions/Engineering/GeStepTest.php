<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\Compare;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\Attributes\DataProvider;

class GeStepTest extends AllSetupTeardown
{
    #[DataProvider('providerGESTEP')]
    public function testDirectCallToGESTEP(int|string $expectedResult, bool|float|int|string $arg1, null|bool|float|int|string $arg2 = null): void
    {
        $result = ($arg2 === null) ? Compare::geStep($arg1) : Compare::geStep($arg1, $arg2);
        self::assertSame($expectedResult, $result);
    }

    #[DataProvider('providerGESTEP')]
    public function testGESTEPAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=GESTEP({$arguments})";

        $result = $calculation->calculateFormula($formula);
        self::assertSame($expectedResult, $result);
    }

    #[DataProvider('providerGESTEP')]
    public function testGESTEPInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $worksheet = $this->getSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=GESTEP({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public static function providerGESTEP(): array
    {
        return require 'tests/data/Calculation/Engineering/GESTEP.php';
    }

    #[DataProvider('providerUnhappyGESTEP')]
    public function testGESTEPUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $worksheet = $this->getSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=GESTEP({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
    }

    public static function providerUnhappyGESTEP(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for GESTEP() function'],
        ];
    }

    #[DataProvider('providerGeStepArray')]
    public function testGeStepArray(array $expectedResult, string $a, string $b): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=GESTEP({$a}, {$b})";
        $result = $calculation->calculateFormula($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerGeStepArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [1, 1, 1, 1, 1],
                    [0, 1, 1, 1, 1],
                    [0, 1, 1, 1, 0],
                    [0, 1, 0, 1, 0],
                    [0, 1, 0, 0, 0],
                ],
                '{-1.2, 2.5, 0.0, 0.25, -0.5}',
                '{-1.2; -0.5; 0.0; 0.25; 2.5}',
            ],
        ];
    }
}

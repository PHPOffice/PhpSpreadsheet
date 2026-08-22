<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ErfC;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\Attributes\DataProvider;

class ErfCTest extends AllSetupTeardown
{
    const ERF_PRECISION = 1E-14;

    #[DataProvider('providerERFC')]
    public function testDirectCallToERFC(mixed $expectedResult, mixed ...$args): void
    {
        $result = ErfC::ERFC(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, self::ERF_PRECISION);
    }

    #[DataProvider('providerERFC')]
    public function testERFCAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=ERFC({$arguments})";

        $result = $calculation->calculateFormula($formula);
        self::assertEqualsWithDelta($expectedResult, $result, self::ERF_PRECISION);
    }

    #[DataProvider('providerERFC')]
    public function testERFCInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $worksheet = $this->getSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=ERFC({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, self::ERF_PRECISION);
    }

    public static function providerERFC(): array
    {
        return require 'tests/data/Calculation/Engineering/ERFC.php';
    }

    #[DataProvider('providerUnhappyERFC')]
    public function testERFCUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $worksheet = $this->getSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=ERFC({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
    }

    public static function providerUnhappyERFC(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for ERFC() function'],
        ];
    }

    #[DataProvider('providerErfCArray')]
    public function testErfCArray(array $expectedResult, string $lower): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ERFC({$lower})";
        $result = $calculation->calculateFormula($formula);
        self::assertEqualsWithDelta($expectedResult, $result, self::ERF_PRECISION);
    }

    public static function providerErfCArray(): array
    {
        return [
            'row vector' => [
                [
                    [1.9103139782296354, 1.5204998778130465, 1.0, 0.7236736098317631, 0.0004069520174449588],
                ],
                '{-1.2, -0.5, 0.0, 0.25, 2.5}',
            ],
        ];
    }
}

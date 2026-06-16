<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\BitWise;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\Attributes\DataProvider;

class BitOrTest extends AllSetupTeardown
{
    #[DataProvider('providerBITOR')]
    public function testDirectCallToBITOR(float|int|string $expectedResult, null|bool|int|float|string $arg1, null|bool|int|float|string $arg2): void
    {
        $result = BitWise::BITOR($arg1, $arg2);
        self::assertSame($expectedResult, $result);
    }

    #[DataProvider('providerBITOR')]
    public function testBITORAsFormula(float|int|string $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=BITOR({$arguments})";

        $result = $calculation->calculateFormula($formula);
        self::assertSame($expectedResult, $result);
    }

    #[DataProvider('providerBITOR')]
    public function testBITORInWorksheet(float|int|string $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $worksheet = $this->getSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=BITOR({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public static function providerBITOR(): array
    {
        return require 'tests/data/Calculation/Engineering/BITOR.php';
    }

    #[DataProvider('providerUnhappyBITOR')]
    public function testBITORUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $worksheet = $this->getSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=BITOR({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
    }

    public static function providerUnhappyBITOR(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for BITOR() function'],
            ['Formula Error: Wrong number of arguments for BITOR() function', 1234],
        ];
    }

    /** @param mixed[] $expectedResult */
    #[DataProvider('providerBitOrArray')]
    public function testBitOrArray(array $expectedResult, string $number1, string $number2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=BITOR({$number1}, {$number2})";
        $result = $calculation->calculateFormula($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerBitOrArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [7, 11, 11],
                    [7, 12, 13],
                    [7, 13, 13],
                ],
                '{7, 8, 9}',
                '{3; 4; 5}',
            ],
        ];
    }
}

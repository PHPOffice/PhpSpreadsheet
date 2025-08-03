<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\BitWise;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\Attributes\DataProvider;

class BitXorTest extends AllSetupTeardown
{
    #[DataProvider('providerBITXOR')]
    public function testDirectCallToBITXOR(float|int|string $expectedResult, null|bool|int|float|string $arg1, null|bool|int|float|string $arg2): void
    {
        $result = BitWise::BITXOR($arg1, $arg2);
        self::assertSame($expectedResult, $result);
    }

    #[DataProvider('providerBITXOR')]
    public function testBITXORAsFormula(float|int|string $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=BITXOR({$arguments})";

        $result = $calculation->calculateFormula($formula);
        self::assertSame($expectedResult, $result);
    }

    #[DataProvider('providerBITXOR')]
    public function testBITXORInWorksheet(float|int|string $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $worksheet = $this->getSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=BITXOR({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public static function providerBITXOR(): array
    {
        return require 'tests/data/Calculation/Engineering/BITXOR.php';
    }

    #[DataProvider('providerUnhappyBITXOR')]
    public function testBITXORUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $worksheet = $this->getSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=BITXOR({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
    }

    public static function providerUnhappyBITXOR(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for BITXOR() function'],
            ['Formula Error: Wrong number of arguments for BITXOR() function', 1234],
        ];
    }

    /** @param mixed[] $expectedResult */
    #[DataProvider('providerBitXorArray')]
    public function testBitXorArray(array $expectedResult, string $number1, string $number2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=BITXOR({$number1}, {$number2})";
        $result = $calculation->calculateFormula($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerBitXorArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [4, 11, 10],
                    [3, 12, 13],
                    [2, 13, 12],
                ],
                '{7, 8, 9}',
                '{3; 4; 5}',
            ],
        ];
    }
}

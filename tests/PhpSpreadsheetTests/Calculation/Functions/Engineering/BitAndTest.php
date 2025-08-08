<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\BitWise;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\Attributes\DataProvider;

class BitAndTest extends AllSetupTeardown
{
    #[DataProvider('providerBITAND')]
    public function testDirectCallToBITAND(float|int|string $expectedResult, null|bool|int|float|string $arg1, null|bool|int|float|string $arg2): void
    {
        $result = BitWise::BITAND($arg1, $arg2);
        self::assertSame($expectedResult, $result);
    }

    #[DataProvider('providerBITAND')]
    public function testBITANDAsFormula(float|int|string $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=BITAND({$arguments})";

        $result = $calculation->calculateFormula($formula);
        self::assertSame($expectedResult, $result);
    }

    #[DataProvider('providerBITAND')]
    public function testBITANDInWorksheet(float|int|string $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $worksheet = $this->getSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=BITAND({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public static function providerBITAND(): array
    {
        return require 'tests/data/Calculation/Engineering/BITAND.php';
    }

    #[DataProvider('providerUnhappyBITAND')]
    public function testBITANDUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $worksheet = $this->getSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=BITAND({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
    }

    public static function providerUnhappyBITAND(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for BITAND() function'],
            ['Formula Error: Wrong number of arguments for BITAND() function', 1234],
        ];
    }

    /** @param mixed[] $expectedResult */
    #[DataProvider('providerBitAndArray')]
    public function testBitAndArray(array $expectedResult, string $number1, string $number2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=BITAND({$number1}, {$number2})";
        $result = $calculation->calculateFormula($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerBitAndArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [3, 0, 1],
                    [4, 0, 0],
                    [5, 0, 1],
                ],
                '{7, 8, 9}',
                '{3; 4; 5}',
            ],
        ];
    }
}

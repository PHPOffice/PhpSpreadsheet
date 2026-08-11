<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\BitWise;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\Attributes\DataProvider;

class BitLShiftTest extends AllSetupTeardown
{
    #[DataProvider('providerBITLSHIFT')]
    public function testDirectCallToBITLSHIFT(float|int|string $expectedResult, null|bool|int|float|string $arg1, null|bool|int|float|string $arg2): void
    {
        $result = BitWise::BITLSHIFT($arg1, $arg2);
        self::assertSame($expectedResult, $result);
    }

    #[DataProvider('providerBITLSHIFT')]
    public function testBITLSHIFTAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=BITLSHIFT({$arguments})";

        $result = $calculation->calculateFormula($formula);
        self::assertSame($expectedResult, $result);
    }

    #[DataProvider('providerBITLSHIFT')]
    public function testBITLSHIFTInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $worksheet = $this->getSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=BITLSHIFT({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public static function providerBITLSHIFT(): array
    {
        return require 'tests/data/Calculation/Engineering/BITLSHIFT.php';
    }

    #[DataProvider('providerUnhappyBITLSHIFT')]
    public function testBITLSHIFTUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $worksheet = $this->getSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=BITLSHIFT({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
    }

    public static function providerUnhappyBITLSHIFT(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for BITLSHIFT() function'],
            ['Formula Error: Wrong number of arguments for BITLSHIFT() function', 1234],
        ];
    }

    /** @param mixed[] $expectedResult */
    #[DataProvider('providerBitLShiftArray')]
    public function testBitLShiftArray(array $expectedResult, string $number, string $bits): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=BITLSHIFT({$number}, {$bits})";
        $result = $calculation->calculateFormula($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerBitLShiftArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [14, 28, 56, 112, 224],
                    [16, 32, 64, 128, 256],
                    [18, 36, 72, 144, 288],
                ],
                '{7; 8; 9}',
                '{1, 2, 3, 4, 5}',
            ],
        ];
    }
}

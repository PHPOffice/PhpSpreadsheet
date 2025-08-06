<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\BitWise;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\Attributes\DataProvider;

class BitRShiftTest extends AllSetupTeardown
{
    #[DataProvider('providerBITRSHIFT')]
    public function testDirectCallToBITRSHIFT(float|int|string $expectedResult, null|bool|int|float|string $arg1, null|bool|int|float|string $arg2): void
    {
        $result = BitWise::BITRSHIFT($arg1, $arg2);
        self::assertSame($expectedResult, $result);
    }

    #[DataProvider('providerBITRSHIFT')]
    public function testBITRSHIFTAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=BITRSHIFT({$arguments})";

        $result = $calculation->calculateFormula($formula);
        self::assertSame($expectedResult, $result);
    }

    #[DataProvider('providerBITRSHIFT')]
    public function testBITRSHIFTInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $worksheet = $this->getSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=BITRSHIFT({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public static function providerBITRSHIFT(): array
    {
        return require 'tests/data/Calculation/Engineering/BITRSHIFT.php';
    }

    #[DataProvider('providerUnhappyBITRSHIFT')]
    public function testBITRSHIFTUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $worksheet = $this->getSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=BITRSHIFT({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
    }

    public static function providerUnhappyBITRSHIFT(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for BITRSHIFT() function'],
            ['Formula Error: Wrong number of arguments for BITRSHIFT() function', 1234],
        ];
    }

    /** @param mixed[] $expectedResult */
    #[DataProvider('providerBitRShiftArray')]
    public function testBitRShiftArray(array $expectedResult, string $number, string $bits): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=BITRSHIFT({$number}, {$bits})";
        $result = $calculation->calculateFormula($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerBitRShiftArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [31, 15, 7, 3, 1],
                    [32, 16, 8, 4, 2],
                    [37, 18, 9, 4, 2],
                ],
                '{63; 64; 75}',
                '{1, 2, 3, 4, 5}',
            ],
        ];
    }
}

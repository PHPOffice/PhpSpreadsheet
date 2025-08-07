<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertDecimal;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\Attributes\DataProvider;

class Dec2BinTest extends AllSetupTeardown
{
    #[DataProvider('providerDEC2BIN')]
    public function testDirectCallToDEC2BIN(mixed $expectedResult, bool|float|int|string $value, null|float|int|string $digits = null): void
    {
        $result = ($digits === null) ? ConvertDecimal::toBinary($value) : ConvertDecimal::toBinary($value, $digits);
        self::assertSame($expectedResult, $result);
    }

    #[DataProvider('providerDEC2BIN')]
    public function testDEC2BINAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=DEC2BIN({$arguments})";

        $result = $calculation->calculateFormula($formula);
        self::assertSame($expectedResult, $result);
    }

    #[DataProvider('providerDEC2BIN')]
    public function testDEC2BINInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $worksheet = $this->getSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=DEC2BIN({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public static function providerDEC2BIN(): array
    {
        return require 'tests/data/Calculation/Engineering/DEC2BIN.php';
    }

    #[DataProvider('providerUnhappyDEC2BIN')]
    public function testDEC2BINUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $worksheet = $this->getSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=DEC2BIN({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
    }

    public static function providerUnhappyDEC2BIN(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for DEC2BIN() function'],
        ];
    }

    #[DataProvider('providerDEC2BINOds')]
    public function testDEC2BINOds(mixed $expectedResult, bool|float|int|string $value, null|float|int|string $digits = null): void
    {
        $this->setOpenOffice();

        $result = ($digits === null) ? ConvertDecimal::toBinary($value) : ConvertDecimal::toBinary($value, $digits);
        self::assertSame($expectedResult, $result);
    }

    public static function providerDEC2BINOds(): array
    {
        return require 'tests/data/Calculation/Engineering/DEC2BINOpenOffice.php';
    }

    public function testDEC2BINFrac(): void
    {
        $calculation = Calculation::getInstance();
        $formula = '=DEC2BIN(5.1)';

        $this->setGnumeric();
        $result = $calculation->calculateFormula($formula);
        self::assertSame('101', $result, 'Gnumeric');

        $this->setOpenOffice();
        $result = $calculation->calculateFormula($formula);
        self::assertSame('101', $result, 'OpenOffice');

        $this->setExcel();
        $result = $calculation->calculateFormula($formula);
        self::assertSame('101', $result, 'Excel');
    }

    /** @param mixed[] $expectedResult */
    #[DataProvider('providerDec2BinArray')]
    public function testDec2BinArray(array $expectedResult, string $value): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=DEC2BIN({$value})";
        $result = $calculation->calculateFormula($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerDec2BinArray(): array
    {
        return [
            'row/column vector' => [
                [['100', '111', '111111', '10011001', '11001100', '101010101']],
                '{4, 7, 63, 153, 204, 341}',
            ],
        ];
    }
}

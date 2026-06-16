<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertBinary;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\Attributes\DataProvider;

class Bin2HexTest extends AllSetupTeardown
{
    #[DataProvider('providerBIN2HEX')]
    public function testDirectCallToBIN2HEX(mixed $expectedResult, bool|float|int|string $value, null|float|int|string $digits = null): void
    {
        $result = ($digits === null) ? ConvertBinary::toHex($value) : ConvertBinary::toHex($value, $digits);
        self::assertSame($expectedResult, $result);
    }

    #[DataProvider('providerBIN2HEX')]
    public function testBIN2HEXAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=BIN2HEX({$arguments})";

        $result = $calculation->calculateFormula($formula);
        self::assertSame($expectedResult, $result);
    }

    #[DataProvider('providerBIN2HEX')]
    public function testBIN2HEXInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $worksheet = $this->getSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=BIN2HEX({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public static function providerBIN2HEX(): array
    {
        return require 'tests/data/Calculation/Engineering/BIN2HEX.php';
    }

    #[DataProvider('providerUnhappyBIN2HEX')]
    public function testBIN2HEXUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $worksheet = $this->getSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=BIN2HEX({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
    }

    public static function providerUnhappyBIN2HEX(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for BIN2HEX() function'],
        ];
    }

    #[DataProvider('providerBIN2HEXOds')]
    public function testBIN2HEXOds(mixed $expectedResult, bool|float|int|string $value, ?int $digits = null): void
    {
        $this->setOpenOffice();
        $result = ($digits === null) ? ConvertBinary::toHex($value) : ConvertBinary::toHex($value, $digits);
        self::assertSame($expectedResult, $result);
    }

    public static function providerBIN2HEXOds(): array
    {
        return require 'tests/data/Calculation/Engineering/BIN2HEXOpenOffice.php';
    }

    public function testBIN2HEXFractional(): void
    {
        $calculation = Calculation::getInstance();
        $formula = '=BIN2HEX(101.1)';

        $this->setGnumeric();
        $result = $calculation->calculateFormula($formula);
        self::assertSame('5', $result, 'Gnumeric');

        $this->setOpenOffice();
        $result = $calculation->calculateFormula($formula);
        self::assertSame(ExcelError::NAN(), $result, 'OpenOffice');

        $this->setExcel();
        $result = $calculation->calculateFormula($formula);
        self::assertSame(ExcelError::NAN(), $result, 'Excel');
    }

    /** @param mixed[] $expectedResult */
    #[DataProvider('providerBin2HexArray')]
    public function testBin2HexArray(array $expectedResult, string $value): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=BIN2HEX({$value})";
        $result = $calculation->calculateFormula($formula);
        self::assertSame($expectedResult, $result);
    }

    public static function providerBin2HexArray(): array
    {
        return [
            'row/column vector' => [
                [['4', '7', '3F', '99', 'CC', '155']],
                '{"100", "111", "111111", "10011001", "11001100", "101010101"}',
            ],
        ];
    }
}

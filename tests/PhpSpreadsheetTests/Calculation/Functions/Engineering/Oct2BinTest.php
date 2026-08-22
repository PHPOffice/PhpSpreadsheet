<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertOctal;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\Attributes\DataProvider;

class Oct2BinTest extends AllSetupTeardown
{
    #[DataProvider('providerOCT2BIN')]
    public function testDirectCallToOCT2BIN(mixed $expectedResult, bool|float|int|string $value, ?int $digits = null): void
    {
        $result = ($digits === null) ? ConvertOctal::toBinary($value) : ConvertOctal::toBinary($value, $digits);
        self::assertSame($expectedResult, $result);
    }

    #[DataProvider('providerOCT2BIN')]
    public function testOCT2BINAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=OCT2BIN({$arguments})";

        $result = $calculation->calculateFormula($formula);
        self::assertSame($expectedResult, $result);
    }

    #[DataProvider('providerOCT2BIN')]
    public function testOCT2BINInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $worksheet = $this->getSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=OCT2BIN({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public static function providerOCT2BIN(): array
    {
        return require 'tests/data/Calculation/Engineering/OCT2BIN.php';
    }

    #[DataProvider('providerUnhappyOCT2BIN')]
    public function testOCT2BINUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $worksheet = $this->getSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=OCT2BIN({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
    }

    public static function providerUnhappyOCT2BIN(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for OCT2BIN() function'],
        ];
    }

    #[DataProvider('providerOCT2BINOds')]
    public function testOCT2BINOds(mixed $expectedResult, bool|float|int|string $value, ?int $digits = null): void
    {
        $this->setOpenOffice();

        $result = ($digits === null) ? ConvertOctal::toBinary($value) : ConvertOctal::toBinary($value, $digits);
        self::assertSame($expectedResult, $result);
    }

    public static function providerOCT2BINOds(): array
    {
        return require 'tests/data/Calculation/Engineering/OCT2BINOpenOffice.php';
    }

    public function testOCT2BINFrac(): void
    {
        $calculation = Calculation::getInstance();
        $formula = '=OCT2BIN(10.1)';

        $this->setGnumeric();
        $result = $calculation->calculateFormula($formula);
        self::assertSame('1000', $result, 'Gnumeric');

        $this->setOpenOffice();
        $result = $calculation->calculateFormula($formula);
        self::assertSame(ExcelError::NAN(), $result, 'OpenOffice');

        $this->setExcel();
        $result = $calculation->calculateFormula($formula);
        self::assertSame(ExcelError::NAN(), $result, 'Excel');
    }

    /** @param mixed[] $expectedResult */
    #[DataProvider('providerOct2BinArray')]
    public function testOct2BinArray(array $expectedResult, string $value): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=OCT2BIN({$value})";
        $result = $calculation->calculateFormula($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerOct2BinArray(): array
    {
        return [
            'row/column vector' => [
                [['100', '111', '111111', '10011001', '11001100', '101010101']],
                '{"4", "7", "77", "231", "314", "525"}',
            ],
        ];
    }
}

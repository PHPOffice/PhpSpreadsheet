<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertOctal;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\Attributes\DataProvider;

class Oct2HexTest extends AllSetupTeardown
{
    #[DataProvider('providerOCT2HEX')]
    public function testDirectCallToOCT2HEX(mixed $expectedResult, bool|float|int|string $value, ?int $digits = null): void
    {
        $result = ($digits === null) ? ConvertOctal::toHex($value) : ConvertOctal::toHex($value, $digits);
        self::assertSame($expectedResult, $result);
    }

    #[DataProvider('providerOCT2HEX')]
    public function testOCT2HEXAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=OCT2HEX({$arguments})";

        $result = $calculation->calculateFormula($formula);
        self::assertSame($expectedResult, $result);
    }

    #[DataProvider('providerOCT2HEX')]
    public function testOCT2HEXInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $worksheet = $this->getSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=OCT2HEX({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public static function providerOCT2HEX(): array
    {
        return require 'tests/data/Calculation/Engineering/OCT2HEX.php';
    }

    #[DataProvider('providerUnhappyOCT2HEX')]
    public function testOCT2HEXUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $worksheet = $this->getSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=OCT2HEX({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
    }

    public static function providerUnhappyOCT2HEX(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for OCT2HEX() function'],
        ];
    }

    #[DataProvider('providerOCT2HEXOds')]
    public function testOCT2HEXOds(mixed $expectedResult, bool|float|int|string $value, ?int $digits = null): void
    {
        $this->setOpenOffice();

        $result = ($digits === null) ? ConvertOctal::toHex($value) : ConvertOctal::toHex($value, $digits);
        self::assertSame($expectedResult, $result);
    }

    public static function providerOCT2HEXOds(): array
    {
        return require 'tests/data/Calculation/Engineering/OCT2HEXOpenOffice.php';
    }

    public function testOCT2HEXFrac(): void
    {
        $calculation = Calculation::getInstance();
        $formula = '=OCT2HEX(20.1)';

        $this->setGnumeric();
        $result = $calculation->calculateFormula($formula);
        self::assertSame('10', $result, 'Gnumeric');

        $this->setOpenOffice();
        $result = $calculation->calculateFormula($formula);
        self::assertSame(ExcelError::NAN(), $result, 'OpenOffice');

        $this->setExcel();
        $result = $calculation->calculateFormula($formula);
        self::assertSame(ExcelError::NAN(), $result, 'Excel');
    }

    /** @param mixed[] $expectedResult */
    #[DataProvider('providerOct2HexArray')]
    public function testOct2HexArray(array $expectedResult, string $value): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=OCT2HEX({$value})";
        $result = $calculation->calculateFormula($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerOct2HexArray(): array
    {
        return [
            'row/column vector' => [
                [['4', '7', '3F', '99', 'CC', '155']],
                '{"4", "7", "77", "231", "314", "525"}',
            ],
        ];
    }
}

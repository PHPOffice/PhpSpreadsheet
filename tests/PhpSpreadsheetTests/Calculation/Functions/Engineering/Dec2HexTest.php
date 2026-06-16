<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertDecimal;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\Attributes\DataProvider;

class Dec2HexTest extends AllSetupTeardown
{
    #[DataProvider('providerDEC2HEX')]
    public function testDirectCallToDEC2HEX(mixed $expectedResult, bool|float|int|string $value, null|float|int|string $digits = null): void
    {
        $result = ($digits === null) ? ConvertDecimal::toHex($value) : ConvertDecimal::toHex($value, $digits);
        self::assertSame($expectedResult, $result);
    }

    #[DataProvider('providerDEC2HEX')]
    public function testDEC2HEXAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=DEC2HEX({$arguments})";

        $result = $calculation->calculateFormula($formula);
        self::assertSame($expectedResult, $result);
    }

    #[DataProvider('providerDEC2HEX')]
    public function testDEC2HEXInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $worksheet = $this->getSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=DEC2HEX({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public static function providerDEC2HEX(): array
    {
        return require 'tests/data/Calculation/Engineering/DEC2HEX.php';
    }

    #[DataProvider('providerUnhappyDEC2HEX')]
    public function testDEC2HEXUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $worksheet = $this->getSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=DEC2HEX({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
    }

    public static function providerUnhappyDEC2HEX(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for DEC2HEX() function'],
        ];
    }

    #[DataProvider('providerDEC2HEXOds')]
    public function testDEC2HEXOds(mixed $expectedResult, bool|float|int|string $value, null|float|int|string $digits = null): void
    {
        $this->setOpenOffice();

        $result = ($digits === null) ? ConvertDecimal::toHex($value) : ConvertDecimal::toHex($value, $digits);
        self::assertSame($expectedResult, $result);
    }

    public static function providerDEC2HEXOds(): array
    {
        return require 'tests/data/Calculation/Engineering/DEC2HEXOpenOffice.php';
    }

    public function testDEC2HEXFrac(): void
    {
        $calculation = Calculation::getInstance();
        $formula = '=DEC2HEX(17.1)';

        $this->setGnumeric();
        $result = $calculation->calculateFormula($formula);
        self::assertSame('11', $result, 'Gnumeric');

        $this->setOpenOffice();
        $result = $calculation->calculateFormula($formula);
        self::assertSame('11', $result, 'OpenOffice');

        $this->setExcel();
        $result = $calculation->calculateFormula($formula);
        self::assertSame('11', $result, 'Excel');
    }

    public function test32bitHex(): void
    {
        self::assertEquals('A2DE246000', ConvertDecimal::hex32bit(-400000000000, 'DE246000', true));
        self::assertEquals('7FFFFFFFFF', ConvertDecimal::hex32bit(549755813887, 'FFFFFFFF', true));
        self::assertEquals('FFFFFFFFFF', ConvertDecimal::hex32bit(-1, 'FFFFFFFF', true));
    }

    /** @param mixed[] $expectedResult */
    #[DataProvider('providerDec2HexArray')]
    public function testDec2HexArray(array $expectedResult, string $value): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=DEC2HEX({$value})";
        $result = $calculation->calculateFormula($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerDec2HexArray(): array
    {
        return [
            'row/column vector' => [
                [['4', '7', '3F', '99', 'CC', '155']],
                '{4, 7, 63, 153, 204, 341}',
            ],
        ];
    }
}

<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertBinary;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\Attributes\DataProvider;

class Bin2OctTest extends AllSetupTeardown
{
    #[DataProvider('providerBIN2OCT')]
    public function testDirectCallToBIN2OCT(mixed $expectedResult, bool|float|int|string $value, null|float|int|string $digits = null): void
    {
        $result = ($digits === null) ? ConvertBinary::toOctal($value) : ConvertBinary::toOctal($value, $digits);
        self::assertSame($expectedResult, $result);
    }

    #[DataProvider('providerBIN2OCT')]
    public function testBIN2OCTAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=BIN2OCT({$arguments})";

        $result = $calculation->calculateFormula($formula);
        self::assertSame($expectedResult, $result);
    }

    #[DataProvider('providerBIN2OCT')]
    public function testBIN2OCTInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $worksheet = $this->getSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=BIN2OCT({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public static function providerBIN2OCT(): array
    {
        return require 'tests/data/Calculation/Engineering/BIN2OCT.php';
    }

    #[DataProvider('providerUnhappyBIN2OCT')]
    public function testBIN2OCTUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $worksheet = $this->getSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=BIN2OCT({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
    }

    public static function providerUnhappyBIN2OCT(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for BIN2OCT() function'],
        ];
    }

    #[DataProvider('providerBIN2OCTOds')]
    public function testBIN2OCTOds(mixed $expectedResult, bool|float|int|string $value, ?int $digits = null): void
    {
        $this->setOpenOffice();

        $result = ($digits === null) ? ConvertBinary::toOctal($value) : ConvertBinary::toOctal($value, $digits);
        self::assertSame($expectedResult, $result);
    }

    public static function providerBIN2OCTOds(): array
    {
        return require 'tests/data/Calculation/Engineering/BIN2OCTOpenOffice.php';
    }

    public function testBIN2OCTFractional(): void
    {
        $calculation = Calculation::getInstance();
        $formula = '=BIN2OCT(101.1)';

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
    #[DataProvider('providerBin2OctArray')]
    public function testBin2OctArray(array $expectedResult, string $value): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=BIN2OCT({$value})";
        $result = $calculation->calculateFormula($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerBin2OctArray(): array
    {
        return [
            'row/column vector' => [
                [['4', '7', '77', '231', '314', '525']],
                '{"100", "111", "111111", "10011001", "11001100", "101010101"}',
            ],
        ];
    }
}

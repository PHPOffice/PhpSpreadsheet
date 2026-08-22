<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertHex;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\Attributes\DataProvider;

class Hex2OctTest extends AllSetupTeardown
{
    #[DataProvider('providerHEX2OCT')]
    public function testDirectCallToHEX2OCT(mixed $expectedResult, bool|float|int|string $value, ?int $digits = null): void
    {
        $result = ($digits === null) ? ConvertHex::toOctal($value) : ConvertHex::toOctal($value, $digits);
        self::assertSame($expectedResult, $result);
    }

    #[DataProvider('providerHEX2OCT')]
    public function testHEX2OCTAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=HEX2OCT({$arguments})";

        $result = $calculation->calculateFormula($formula);
        self::assertSame($expectedResult, $result);
    }

    #[DataProvider('providerHEX2OCT')]
    public function testHEX2OCTInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $worksheet = $this->getSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=HEX2OCT({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public static function providerHEX2OCT(): array
    {
        return require 'tests/data/Calculation/Engineering/HEX2OCT.php';
    }

    #[DataProvider('providerUnhappyHEX2OCT')]
    public function testHEX2OCTUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $worksheet = $this->getSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=HEX2OCT({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
    }

    public static function providerUnhappyHEX2OCT(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for HEX2OCT() function'],
        ];
    }

    #[DataProvider('providerHEX2OCTOds')]
    public function testHEX2OCTOds(mixed $expectedResult, bool|float|int|string $value, ?int $digits = null): void
    {
        $this->setOpenOffice();

        $result = ($digits === null) ? ConvertHex::toOctal($value) : ConvertHex::toOctal($value, $digits);
        self::assertSame($expectedResult, $result);
    }

    public static function providerHEX2OCTOds(): array
    {
        return require 'tests/data/Calculation/Engineering/HEX2OCTOpenOffice.php';
    }

    public function testHEX2OCTFrac(): void
    {
        $calculation = Calculation::getInstance();
        $formula = '=HEX2OCT(10.1)';

        $this->setGnumeric();
        $result = $calculation->calculateFormula($formula);
        self::assertSame('20', $result, 'Gnumeric');

        $this->setOpenOffice();
        $result = $calculation->calculateFormula($formula);
        self::assertSame(ExcelError::NAN(), $result, 'OpenOffice');

        $this->setExcel();
        $result = $calculation->calculateFormula($formula);
        self::assertSame(ExcelError::NAN(), $result, 'Excel');
    }

    #[DataProvider('providerHex2OctArray')]
    public function testHex2OctArray(array $expectedResult, string $value): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=HEX2OCT({$value})";
        $result = $calculation->calculateFormula($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerHex2OctArray(): array
    {
        return [
            'row/column vector' => [
                [['4', '7', '77', '231', '314', '525']],
                '{"4", "7", "3F", "99", "CC", "155"}',
            ],
        ];
    }
}

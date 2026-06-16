<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertDecimal;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\Attributes\DataProvider;

class Dec2OctTest extends AllSetupTeardown
{
    #[DataProvider('providerDEC2OCT')]
    public function testDirectCallToDEC2OCT(mixed $expectedResult, bool|float|int|string $value, ?int $digits = null): void
    {
        $result = ($digits === null) ? ConvertDecimal::toOctal($value) : ConvertDecimal::toOctal($value, $digits);
        self::assertSame($expectedResult, $result);
    }

    #[DataProvider('providerDEC2OCT')]
    public function testDEC2OCTAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=DEC2OCT({$arguments})";

        $result = $calculation->calculateFormula($formula);
        self::assertSame($expectedResult, $result);
    }

    #[DataProvider('providerDEC2OCT')]
    public function testDEC2OCTInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $worksheet = $this->getSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=DEC2OCT({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public static function providerDEC2OCT(): array
    {
        return require 'tests/data/Calculation/Engineering/DEC2OCT.php';
    }

    #[DataProvider('providerUnhappyDEC2OCT')]
    public function testDEC2OCTUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $worksheet = $this->getSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=DEC2OCT({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
    }

    public static function providerUnhappyDEC2OCT(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for DEC2OCT() function'],
        ];
    }

    #[DataProvider('providerDEC2OCTOds')]
    public function testDEC2OCTOds(mixed $expectedResult, bool|float|int|string $value, ?int $digits = null): void
    {
        $this->setOpenOffice();

        $result = ($digits === null) ? ConvertDecimal::toOctal($value) : ConvertDecimal::toOctal($value, $digits);
        self::assertSame($expectedResult, $result);
    }

    public static function providerDEC2OCTOds(): array
    {
        return require 'tests/data/Calculation/Engineering/DEC2OCTOpenOffice.php';
    }

    public function testDEC2OCTFrac(): void
    {
        $calculation = Calculation::getInstance();
        $formula = '=DEC2OCT(17.1)';

        $this->setGnumeric();
        $result = $calculation->calculateFormula($formula);
        self::assertSame('21', $result, 'Gnumeric');

        $this->setOpenOffice();
        $result = $calculation->calculateFormula($formula);
        self::assertSame('21', $result, 'OpenOffice');

        $this->setExcel();
        $result = $calculation->calculateFormula($formula);
        self::assertSame('21', $result, 'Excel');
    }

    /** @param mixed[] $expectedResult */
    #[DataProvider('providerDec2OctArray')]
    public function testDec2OctArray(array $expectedResult, string $value): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=DEC2OCT({$value})";
        $result = $calculation->calculateFormula($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerDec2OctArray(): array
    {
        return [
            'row/column vector' => [
                [['4', '7', '77', '231', '314', '525']],
                '{4, 7, 63, 153, 204, 341}',
            ],
        ];
    }
}

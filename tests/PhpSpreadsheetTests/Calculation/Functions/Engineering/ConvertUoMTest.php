<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertUOM;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\TestCase;

class ConvertUoMTest extends TestCase
{
    const UOM_PRECISION = 1E-12;

    public function testGetConversionGroups(): void
    {
        $result = ConvertUOM::getConversionCategories();
        self::assertContains('Weight and Mass', $result);
    }

    public function testGetConversionGroupUnits(): void
    {
        $result = ConvertUOM::getConversionCategoryUnits();
        self::assertArrayHasKey('Speed', $result);
        self::assertIsArray($result['Speed']);
        self::assertContains('mph', $result['Speed']);
    }

    public function testGetConversionGroupUnitDetails(): void
    {
        $result = ConvertUOM::getConversionCategoryUnitDetails();
        self::assertArrayHasKey('Information', $result);
        self::assertIsArray($result['Information']);
        self::assertContains(['unit' => 'byte', 'description' => 'Byte'], $result['Information']);
    }

    public function testGetConversionMultipliers(): void
    {
        $result = ConvertUOM::getConversionMultipliers();
        self::assertArrayHasKey('k', $result);
        self::assertSame(['multiplier' => 1000.0, 'name' => 'kilo'], $result['k']);
    }

    public function testGetBinaryConversionMultipliers(): void
    {
        $result = ConvertUOM::getBinaryConversionMultipliers();
        self::assertArrayHasKey('ki', $result);
        self::assertSame(['multiplier' => 1024, 'name' => 'kibi'], $result['ki']);
    }

    /**
     * @dataProvider providerCONVERTUOM
     */
    public function testDirectCallToCONVERTUOM(float|int|string $expectedResult, float|int|string $value, string $from, string $to): void
    {
        $result = ConvertUOM::convert($value, $from, $to);
        self::assertEqualsWithDelta($expectedResult, $result, self::UOM_PRECISION);
    }

    /**
     * @dataProvider providerCONVERTUOM
     */
    public function testCONVERTUOMAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=CONVERT({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, self::UOM_PRECISION);
    }

    /**
     * @dataProvider providerCONVERTUOM
     */
    public function testCONVERTUOMInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=CONVERT({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, self::UOM_PRECISION);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerCONVERTUOM(): array
    {
        return require 'tests/data/Calculation/Engineering/CONVERTUOM.php';
    }

    /**
     * @dataProvider providerUnhappyCONVERTUOM
     */
    public function testCONVERTUOMUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=CONVERT({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyCONVERTUOM(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for CONVERT() function'],
            ['Formula Error: Wrong number of arguments for CONVERT() function', 12.34],
            ['Formula Error: Wrong number of arguments for CONVERT() function', 12.34, 'kg'],
        ];
    }

    /**
     * @dataProvider providerConvertUoMArray
     */
    public function testConvertUoMArray(array $expectedResult, string $value, string $fromUoM, string $toUoM): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=CONVERT({$value}, {$fromUoM}, {$toUoM})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, self::UOM_PRECISION);
    }

    public static function providerConvertUoMArray(): array
    {
        return [
            'Weight/Mass' => [
                [
                    [71.42857142857142, 0.15747304441777],
                    [453.5923699999991, 1.0],
                ],
                '1000',
                '{"lbm", "g"}',
                '{"stone"; "kg"}',
            ],
            'Distance' => [
                [
                    [2025371.8285214372, 1093.6132983377101],
                    [1851.9999999999984, 1.0],
                ],
                '1000',
                '{"Nmi", "m"}',
                '{"yd"; "km"}',
            ],
            'Volume' => [
                [
                    [2.976190476190475, 0.00628981077043211],
                    [473.1764729999994, 1.0],
                ],
                '1000',
                '{"pt", "ml"}',
                '{"barrel"; "l"}',
            ],
            'Area' => [
                [
                    [999.9960000040016, 0.247104393046628],
                    [404.6856422400005, 0.1],
                ],
                '1000',
                '{"uk_acre", "m2"}',
                '{"us_acre"; "ha"}',
            ],
        ];
    }
}

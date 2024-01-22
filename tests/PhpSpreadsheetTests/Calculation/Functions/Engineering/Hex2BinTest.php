<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertHex;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\TestCase;

class Hex2BinTest extends TestCase
{
    private string $compatibilityMode;

    protected function setUp(): void
    {
        $this->compatibilityMode = Functions::getCompatibilityMode();
    }

    protected function tearDown(): void
    {
        Functions::setCompatibilityMode($this->compatibilityMode);
    }

    /**
     * @dataProvider providerHEX2BIN
     */
    public function testDirectCallToHEX2BIN(mixed $expectedResult, bool|float|int|string $value, ?int $digits = null): void
    {
        $result = ($digits === null) ? ConvertHex::toBinary($value) : ConvertHex::toBinary($value, $digits);
        self::assertSame($expectedResult, $result);
    }

    private function trimIfQuoted(string $value): string
    {
        return trim($value, '"');
    }

    /**
     * @dataProvider providerHEX2BIN
     */
    public function testHEX2BINAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=HEX2BIN({$arguments})";

        /** @var float|int|string */
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame($expectedResult, $this->trimIfQuoted((string) $result));
    }

    /**
     * @dataProvider providerHEX2BIN
     */
    public function testHEX2BINInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=HEX2BIN({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerHEX2BIN(): array
    {
        return require 'tests/data/Calculation/Engineering/HEX2BIN.php';
    }

    /**
     * @dataProvider providerUnhappyHEX2BIN
     */
    public function testHEX2BINUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=HEX2BIN({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyHEX2BIN(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for HEX2BIN() function'],
        ];
    }

    /**
     * @dataProvider providerHEX2BINOds
     */
    public function testHEX2BINOds(mixed $expectedResult, bool|float|int|string $value, ?int $digits = null): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);

        $result = ($digits === null) ? ConvertHex::toBinary($value) : ConvertHex::toBinary($value, $digits);
        self::assertSame($expectedResult, $result);
    }

    public static function providerHEX2BINOds(): array
    {
        return require 'tests/data/Calculation/Engineering/HEX2BINOpenOffice.php';
    }

    public function testHEX2BINFrac(): void
    {
        $calculation = Calculation::getInstance();
        $formula = '=HEX2BIN(10.1)';

        Functions::setCompatibilityMode(Functions::COMPATIBILITY_GNUMERIC);
        /** @var float|int|string */
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame('10000', $this->trimIfQuoted((string) $result), 'Gnumeric');

        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);
        /** @var float|int|string */
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame(ExcelError::NAN(), $this->trimIfQuoted((string) $result), 'OpenOffice');

        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        /** @var float|int|string */
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame(ExcelError::NAN(), $this->trimIfQuoted((string) $result), 'Excel');
    }

    /**
     * @dataProvider providerHex2BinArray
     */
    public function testHex2BinArray(array $expectedResult, string $value): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=HEX2BIN({$value})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerHex2BinArray(): array
    {
        return [
            'row/column vector' => [
                [['100', '111', '111111', '10011001', '11001100', '101010101']],
                '{"4", "7", "3F", "99", "CC", "155"}',
            ],
        ];
    }
}

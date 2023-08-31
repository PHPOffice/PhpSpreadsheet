<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertOctal;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\TestCase;

class Oct2BinTest extends TestCase
{
    /**
     * @var string
     */
    private $compatibilityMode;

    protected function setUp(): void
    {
        $this->compatibilityMode = Functions::getCompatibilityMode();
    }

    protected function tearDown(): void
    {
        Functions::setCompatibilityMode($this->compatibilityMode);
    }

    /**
     * @dataProvider providerOCT2BIN
     *
     * @param mixed $expectedResult
     */
    public function testDirectCallToOCT2BIN($expectedResult, ...$args): void
    {
        /** @scrutinizer ignore-call */
        $result = ConvertOctal::toBinary(...$args);
        self::assertSame($expectedResult, $result);
    }

    private function trimIfQuoted(string $value): string
    {
        return trim($value, '"');
    }

    /**
     * @dataProvider providerOCT2BIN
     *
     * @param mixed $expectedResult
     */
    public function testOCT2BINAsFormula($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=OCT2BIN({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame($expectedResult, $this->trimIfQuoted((string) $result));
    }

    /**
     * @dataProvider providerOCT2BIN
     *
     * @param mixed $expectedResult
     */
    public function testOCT2BINInWorksheet($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=OCT2BIN({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerOCT2BIN(): array
    {
        return require 'tests/data/Calculation/Engineering/OCT2BIN.php';
    }

    /**
     * @dataProvider providerUnhappyOCT2BIN
     */
    public function testOCT2BINUnhappyPath(string $expectedException, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=OCT2BIN({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyOCT2BIN(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for OCT2BIN() function'],
        ];
    }

    /**
     * @dataProvider providerOCT2BINOds
     *
     * @param mixed $expectedResult
     */
    public function testOCT2BINOds($expectedResult, ...$args): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);

        /** @scrutinizer ignore-call */
        $result = ConvertOctal::toBinary(...$args);
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

        Functions::setCompatibilityMode(Functions::COMPATIBILITY_GNUMERIC);
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame('1000', $this->trimIfQuoted((string) $result), 'Gnumeric');

        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame(ExcelError::NAN(), $this->trimIfQuoted((string) $result), 'OpenOffice');

        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame(ExcelError::NAN(), $this->trimIfQuoted((string) $result), 'Excel');
    }

    /**
     * @dataProvider providerOct2BinArray
     */
    public function testOct2BinArray(array $expectedResult, string $value): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=OCT2BIN({$value})";
        $result = $calculation->_calculateFormulaValue($formula);
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

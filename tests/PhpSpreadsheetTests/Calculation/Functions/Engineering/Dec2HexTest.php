<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertDecimal;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\TestCase;

class Dec2HexTest extends TestCase
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
     * @dataProvider providerDEC2HEX
     *
     * @param mixed $expectedResult
     */
    public function testDirectCallToDEC2HEX($expectedResult, ...$args): void
    {
        /** @scrutinizer ignore-call */
        $result = ConvertDecimal::toHex(...$args);
        self::assertSame($expectedResult, $result);
    }

    private function trimIfQuoted(string $value): string
    {
        return trim($value, '"');
    }

    /**
     * @dataProvider providerDEC2HEX
     *
     * @param mixed $expectedResult
     */
    public function testDEC2HEXAsFormula($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=DEC2HEX({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame($expectedResult, $this->trimIfQuoted((string) $result));
    }

    /**
     * @dataProvider providerDEC2HEX
     *
     * @param mixed $expectedResult
     */
    public function testDEC2HEXInWorksheet($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=DEC2HEX({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerDEC2HEX(): array
    {
        return require 'tests/data/Calculation/Engineering/DEC2HEX.php';
    }

    /**
     * @dataProvider providerUnhappyDEC2HEX
     */
    public function testDEC2HEXUnhappyPath(string $expectedException, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=DEC2HEX({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyDEC2HEX(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for DEC2HEX() function'],
        ];
    }

    /**
     * @dataProvider providerDEC2HEXOds
     *
     * @param mixed $expectedResult
     */
    public function testDEC2HEXOds($expectedResult, ...$args): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);

        /** @scrutinizer ignore-call */
        $result = ConvertDecimal::toHex(...$args);
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

        Functions::setCompatibilityMode(Functions::COMPATIBILITY_GNUMERIC);
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame('11', $this->trimIfQuoted((string) $result), 'Gnumeric');

        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame('11', $this->trimIfQuoted((string) $result), 'OpenOffice');

        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame('11', $this->trimIfQuoted((string) $result), 'Excel');
    }

    public function test32bitHex(): void
    {
        self::assertEquals('A2DE246000', ConvertDecimal::hex32bit(-400000000000, 'DE246000', true));
        self::assertEquals('7FFFFFFFFF', ConvertDecimal::hex32bit(549755813887, 'FFFFFFFF', true));
        self::assertEquals('FFFFFFFFFF', ConvertDecimal::hex32bit(-1, 'FFFFFFFF', true));
    }

    /**
     * @dataProvider providerDec2HexArray
     */
    public function testDec2HexArray(array $expectedResult, string $value): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=DEC2HEX({$value})";
        $result = $calculation->_calculateFormulaValue($formula);
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

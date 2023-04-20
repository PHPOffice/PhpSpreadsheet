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

class Oct2DecTest extends TestCase
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
     * @dataProvider providerOCT2DEC
     *
     * @param mixed $expectedResult
     */
    public function testDirectCallToOCT2DEC($expectedResult, ...$args): void
    {
        /** @scrutinizer ignore-call */
        $result = ConvertOctal::toDecimal(...$args);
        self::assertSame($expectedResult, $result);
    }

    private function trimIfQuoted(string $value): string
    {
        return trim($value, '"');
    }

    /**
     * @dataProvider providerOCT2DEC
     *
     * @param mixed $expectedResult
     */
    public function testOCT2DECAsFormula($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=OCT2DEC({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame($expectedResult, $this->trimIfQuoted((string) $result));
    }

    /**
     * @dataProvider providerOCT2DEC
     *
     * @param mixed $expectedResult
     */
    public function testOCT2DECInWorksheet($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=OCT2DEC({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerOCT2DEC(): array
    {
        return require 'tests/data/Calculation/Engineering/OCT2DEC.php';
    }

    /**
     * @dataProvider providerUnhappyOCT2DEC
     */
    public function testOCT2DECUnhappyPath(string $expectedException, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=OCT2DEC({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyOCT2DEC(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for OCT2DEC() function'],
        ];
    }

    /**
     * @dataProvider providerOCT2DECOds
     *
     * @param mixed $expectedResult
     */
    public function testOCT2DECOds($expectedResult, ...$args): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);

        /** @scrutinizer ignore-call */
        $result = ConvertOctal::toDecimal(...$args);
        self::assertSame($expectedResult, $result);
    }

    public static function providerOCT2DECOds(): array
    {
        return require 'tests/data/Calculation/Engineering/OCT2DECOpenOffice.php';
    }

    public function testOCT2DECFrac(): void
    {
        $calculation = Calculation::getInstance();
        $formula = '=OCT2DEC(10.1)';

        Functions::setCompatibilityMode(Functions::COMPATIBILITY_GNUMERIC);
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame('8', $this->trimIfQuoted((string) $result), 'Gnumeric');

        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame(ExcelError::NAN(), $this->trimIfQuoted((string) $result), 'OpenOffice');

        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame(ExcelError::NAN(), $this->trimIfQuoted((string) $result), 'Excel');
    }

    /**
     * @dataProvider providerOct2DecArray
     */
    public function testOct2DecArray(array $expectedResult, string $value): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=OCT2DEC({$value})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerOct2DecArray(): array
    {
        return [
            'row/column vector' => [
                [[4, 7, 63, 153, 204, 341]],
                '{"4", "7", "77", "231", "314", "525"}',
            ],
        ];
    }
}

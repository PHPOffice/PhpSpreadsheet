<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertHex;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\TestCase;

class Hex2OctTest extends TestCase
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
     * @dataProvider providerHEX2OCT
     *
     * @param mixed $expectedResult
     */
    public function testDirectCallToHEX2OCT($expectedResult, ...$args): void
    {
        /** @scrutinizer ignore-call */
        $result = ConvertHex::toOctal(...$args);
        self::assertSame($expectedResult, $result);
    }

    private function trimIfQuoted(string $value): string
    {
        return trim($value, '"');
    }

    /**
     * @dataProvider providerHEX2OCT
     *
     * @param mixed $expectedResult
     */
    public function testHEX2OCTAsFormula($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=HEX2OCT({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame($expectedResult, $this->trimIfQuoted((string) $result));
    }

    /**
     * @dataProvider providerHEX2OCT
     *
     * @param mixed $expectedResult
     */
    public function testHEX2OCTInWorksheet($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=HEX2OCT({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerHEX2OCT(): array
    {
        return require 'tests/data/Calculation/Engineering/HEX2OCT.php';
    }

    /**
     * @dataProvider providerUnhappyHEX2OCT
     */
    public function testHEX2OCTUnhappyPath(string $expectedException, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=HEX2OCT({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyHEX2OCT(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for HEX2OCT() function'],
        ];
    }

    /**
     * @dataProvider providerHEX2OCTOds
     *
     * @param mixed $expectedResult
     */
    public function testHEX2OCTOds($expectedResult, ...$args): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);

        /** @scrutinizer ignore-call */
        $result = ConvertHex::toOctal(...$args);
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

        Functions::setCompatibilityMode(Functions::COMPATIBILITY_GNUMERIC);
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame('20', $this->trimIfQuoted((string) $result), 'Gnumeric');

        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame(ExcelError::NAN(), $this->trimIfQuoted((string) $result), 'OpenOffice');

        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame(ExcelError::NAN(), $this->trimIfQuoted((string) $result), 'Excel');
    }

    /**
     * @dataProvider providerHex2OctArray
     */
    public function testHex2OctArray(array $expectedResult, string $value): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=HEX2OCT({$value})";
        $result = $calculation->_calculateFormulaValue($formula);
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

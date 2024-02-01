<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexOperations;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PhpOffice\PhpSpreadsheetTests\Custom\ComplexAssert;
use PHPUnit\Framework\TestCase;

class ImDivTest extends TestCase
{
    const COMPLEX_PRECISION = 1E-12;

    private ComplexAssert $complexAssert;

    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        $this->complexAssert = new ComplexAssert();
    }

    /**
     * @dataProvider providerIMDIV
     */
    public function testDirectCallToIMDIV(string $expectedResult, string $dividend, string $divisor): void
    {
        $result = ComplexOperations::IMDIV($dividend, $divisor);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    private function trimIfQuoted(string $value): string
    {
        return trim($value, '"');
    }

    /**
     * @dataProvider providerIMDIV
     */
    public function testIMDIVAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=IMDIV({$arguments})";

        /** @var float|int|string */
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $this->trimIfQuoted((string) $result), self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    /**
     * @dataProvider providerIMDIV
     */
    public function testIMDIVInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMDIV({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerIMDIV(): array
    {
        return require 'tests/data/Calculation/Engineering/IMDIV.php';
    }

    /**
     * @dataProvider providerUnhappyIMDIV
     */
    public function testIMDIVUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMDIV({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyIMDIV(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for IMDIV() function'],
            ['Formula Error: Wrong number of arguments for IMDIV() function', '1.23+4.56i'],
        ];
    }

    /**
     * @dataProvider providerImDivArray
     */
    public function testImDivArray(array $expectedResult, string $dividend, string $divisor): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMDIV({$dividend}, {$divisor})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerImDivArray(): array
    {
        return [
            'matrix' => [
                [
                    ['-0.36206896551724+0.3448275862069i', '-1.25i', '-0.375-0.875i'],
                    ['-0.10344827586207+0.24137931034483i', '-0.5i', '-0.5i'],
                    ['0.24137931034483+0.10344827586207i', '0.5i', '0.5'],
                    ['0.5', '1.25i', '0.875+0.375i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
                '{"-2+5i", 2, "2+2i"}',
            ],
        ];
    }
}

<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PhpOffice\PhpSpreadsheetTests\Custom\ComplexAssert;
use PHPUnit\Framework\TestCase;

class ImLnTest extends TestCase
{
    const COMPLEX_PRECISION = 1E-12;

    /**
     * @var ComplexAssert
     */
    private $complexAssert;

    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        $this->complexAssert = new ComplexAssert();
    }

    /**
     * @dataProvider providerIMLN
     *
     * @param mixed $expectedResult
     */
    public function testDirectCallToIMLN($expectedResult, ...$args): void
    {
        /** @scrutinizer ignore-call */
        $result = ComplexFunctions::IMLN(...$args);
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
     * @dataProvider providerIMLN
     *
     * @param mixed $expectedResult
     */
    public function testIMLNAsFormula($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=IMLN({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $this->trimIfQuoted((string) $result), self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    /**
     * @dataProvider providerIMLN
     *
     * @param mixed $expectedResult
     */
    public function testIMLNInWorksheet($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMLN({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerIMLN(): array
    {
        return require 'tests/data/Calculation/Engineering/IMLN.php';
    }

    /**
     * @dataProvider providerUnhappyIMLN
     */
    public function testIMLNUnhappyPath(string $expectedException, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMLN({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyIMLN(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for IMLN() function'],
        ];
    }

    /**
     * @dataProvider providerImLnArray
     */
    public function testImLnArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMLN({$complex})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerImLnArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['0.99050073443329-1.9513027039073i', '0.91629073187416-1.5707963267949i', '0.99050073443329-1.1902899496825i'],
                    ['0.34657359027997-2.3561944901923i', '-1.5707963267949i', '0.34657359027997-0.78539816339745i'],
                    ['0.34657359027997+2.3561944901923i', '1.5707963267949i', '0.34657359027997+0.78539816339745i'],
                    ['0.99050073443329+1.9513027039073i', '0.91629073187416+1.5707963267949i', '0.99050073443329+1.1902899496825i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}

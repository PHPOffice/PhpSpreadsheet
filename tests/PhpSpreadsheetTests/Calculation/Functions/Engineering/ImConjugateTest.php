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

class ImConjugateTest extends TestCase
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
     * @dataProvider providerIMCONJUGATE
     *
     * @param mixed $expectedResult
     */
    public function testDirectCallToIMCONJUGATE($expectedResult, ...$args): void
    {
        /** @scrutinizer ignore-call */
        $result = ComplexFunctions::IMCONJUGATE(...$args);
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
     * @dataProvider providerIMCONJUGATE
     *
     * @param mixed $expectedResult
     */
    public function testIMCONJUGATEAsFormula($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=IMCONJUGATE({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $this->trimIfQuoted((string) $result), self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    /**
     * @dataProvider providerIMCONJUGATE
     *
     * @param mixed $expectedResult
     */
    public function testIMCONJUGATEInWorksheet($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMCONJUGATE({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerIMCONJUGATE(): array
    {
        return require 'tests/data/Calculation/Engineering/IMCONJUGATE.php';
    }

    /**
     * @dataProvider providerUnhappyIMCONJUGATE
     */
    public function testIMCONJUGATEUnhappyPath(string $expectedException, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMCONJUGATE({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyIMCONJUGATE(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for IMCONJUGATE() function'],
        ];
    }

    /**
     * @dataProvider providerImConjugateArray
     */
    public function testImConjugateArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMCONJUGATE({$complex})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerImConjugateArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['-1+2.5i', '2.5i', '1+2.5i'],
                    ['-1+i', 'i', '1+i'],
                    ['-1-i', '-i', '1-i'],
                    ['-1-2.5i', '-2.5i', '1-2.5i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}

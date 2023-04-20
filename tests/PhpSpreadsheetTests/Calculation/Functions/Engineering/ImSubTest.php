<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexOperations;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PhpOffice\PhpSpreadsheetTests\Custom\ComplexAssert;
use PHPUnit\Framework\TestCase;

class ImSubTest extends TestCase
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
     * @dataProvider providerIMSUB
     *
     * @param mixed $expectedResult
     */
    public function testDirectCallToIMSUB($expectedResult, ...$args): void
    {
        /** @scrutinizer ignore-call */
        $result = ComplexOperations::IMSUB(...$args);
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
     * @dataProvider providerIMSUB
     *
     * @param mixed $expectedResult
     */
    public function testIMSUBAsFormula($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=IMSUB({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $this->trimIfQuoted((string) $result), self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    /**
     * @dataProvider providerIMSUB
     *
     * @param mixed $expectedResult
     */
    public function testIMSUBInWorksheet($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMSUB({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerIMSUB(): array
    {
        return require 'tests/data/Calculation/Engineering/IMSUB.php';
    }

    /**
     * @dataProvider providerUnhappyIMSUB
     */
    public function testIMSUBUnhappyPath(string $expectedException, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMSUB({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyIMSUB(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for IMSUB() function'],
            ['Formula Error: Wrong number of arguments for IMSUB() function', '1.23+4.56i'],
        ];
    }

    /**
     * @dataProvider providerImSubArray
     */
    public function testImSubArray(array $expectedResult, string $subidend, string $subisor): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMSUB({$subidend}, {$subisor})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerImSubArray(): array
    {
        return [
            'matrix' => [
                [
                    ['1-7.5i', '-2-2.5i', '-1-4.5i'],
                    ['1-6i', '-2-i', '-1-3i'],
                    ['1-4i', '-2+i', '-1-i'],
                    ['1-2.5i', '-2+2.5i', '-1+0.5i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
                '{"-2+5i", 2, "2+2i"}',
            ],
        ];
    }
}

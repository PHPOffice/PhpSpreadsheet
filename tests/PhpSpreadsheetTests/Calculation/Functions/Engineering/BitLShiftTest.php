<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\BitWise;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\TestCase;

class BitLShiftTest extends TestCase
{
    /**
     * @dataProvider providerBITLSHIFT
     *
     * @param mixed $expectedResult
     */
    public function testDirectCallToBITLSHIFT($expectedResult, ...$args): void
    {
        $result = BitWise::BITLSHIFT(...$args);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider providerBITLSHIFT
     *
     * @param mixed $expectedResult
     */
    public function testBITLSHIFTAsFormula($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=BITLSHIFT({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider providerBITLSHIFT
     *
     * @param mixed $expectedResult
     */
    public function testBITLSHIFTInWorksheet($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=BITLSHIFT({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerBITLSHIFT(): array
    {
        return require 'tests/data/Calculation/Engineering/BITLSHIFT.php';
    }

    /**
     * @dataProvider providerUnhappyBITLSHIFT
     */
    public function testBITLSHIFTUnhappyPath(string $expectedException, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=BITLSHIFT({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyBITLSHIFT(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for BITLSHIFT() function'],
            ['Formula Error: Wrong number of arguments for BITLSHIFT() function', 1234],
        ];
    }

    /**
     * @dataProvider providerBitLShiftArray
     */
    public function testBitLShiftArray(array $expectedResult, string $number, string $bits): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=BITLSHIFT({$number}, {$bits})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerBitLShiftArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [14, 28, 56, 112, 224],
                    [16, 32, 64, 128, 256],
                    [18, 36, 72, 144, 288],
                ],
                '{7; 8; 9}',
                '{1, 2, 3, 4, 5}',
            ],
        ];
    }
}

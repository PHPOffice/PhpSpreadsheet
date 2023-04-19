<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\BitWise;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\TestCase;

class BitRShiftTest extends TestCase
{
    /**
     * @dataProvider providerBITRSHIFT
     *
     * @param mixed $expectedResult
     */
    public function testDirectCallToBITRSHIFT($expectedResult, ...$args): void
    {
        $result = BitWise::BITRSHIFT(...$args);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider providerBITRSHIFT
     *
     * @param mixed $expectedResult
     */
    public function testBITRSHIFTAsFormula($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=BITRSHIFT({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider providerBITRSHIFT
     *
     * @param mixed $expectedResult
     */
    public function testBITRSHIFTInWorksheet($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=BITRSHIFT({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerBITRSHIFT(): array
    {
        return require 'tests/data/Calculation/Engineering/BITRSHIFT.php';
    }

    /**
     * @dataProvider providerUnhappyBITRSHIFT
     */
    public function testBITRSHIFTUnhappyPath(string $expectedException, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=BITRSHIFT({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyBITRSHIFT(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for BITRSHIFT() function'],
            ['Formula Error: Wrong number of arguments for BITRSHIFT() function', 1234],
        ];
    }

    /**
     * @dataProvider providerBitRShiftArray
     */
    public function testBitRShiftArray(array $expectedResult, string $number, string $bits): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=BITRSHIFT({$number}, {$bits})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerBitRShiftArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [31, 15, 7, 3, 1],
                    [32, 16, 8, 4, 2],
                    [37, 18, 9, 4, 2],
                ],
                '{63; 64; 75}',
                '{1, 2, 3, 4, 5}',
            ],
        ];
    }
}

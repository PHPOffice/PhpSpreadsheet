<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\BitWise;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\TestCase;

class BitXorTest extends TestCase
{
    /**
     * @dataProvider providerBITXOR
     *
     * @param mixed $expectedResult
     */
    public function testDirectCallToBITXOR($expectedResult, ...$args): void
    {
        $result = BitWise::BITXOR(...$args);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider providerBITXOR
     *
     * @param mixed $expectedResult
     */
    public function testBITXORAsFormula($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=BITXOR({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider providerBITXOR
     *
     * @param mixed $expectedResult
     */
    public function testBITXORInWorksheet($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=BITXOR({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerBITXOR(): array
    {
        return require 'tests/data/Calculation/Engineering/BITXOR.php';
    }

    /**
     * @dataProvider providerUnhappyBITXOR
     */
    public function testBITXORUnhappyPath(string $expectedException, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=BITXOR({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyBITXOR(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for BITXOR() function'],
            ['Formula Error: Wrong number of arguments for BITXOR() function', 1234],
        ];
    }

    /**
     * @dataProvider providerBitXorArray
     */
    public function testBitXorArray(array $expectedResult, string $number1, string $number2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=BITXOR({$number1}, {$number2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerBitXorArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [4, 11, 10],
                    [3, 12, 13],
                    [2, 13, 12],
                ],
                '{7, 8, 9}',
                '{3; 4; 5}',
            ],
        ];
    }
}

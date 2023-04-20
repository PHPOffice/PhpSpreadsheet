<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\BitWise;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\TestCase;

class BitOrTest extends TestCase
{
    /**
     * @dataProvider providerBITOR
     *
     * @param mixed $expectedResult
     */
    public function testDirectCallToBITOR($expectedResult, ...$args): void
    {
        $result = BitWise::BITOR(...$args);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider providerBITOR
     *
     * @param mixed $expectedResult
     */
    public function testBITORAsFormula($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=BITOR({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider providerBITOR
     *
     * @param mixed $expectedResult
     */
    public function testBITORInWorksheet($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=BITOR({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerBITOR(): array
    {
        return require 'tests/data/Calculation/Engineering/BITOR.php';
    }

    /**
     * @dataProvider providerUnhappyBITOR
     */
    public function testBITORUnhappyPath(string $expectedException, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=BITOR({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyBITOR(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for BITOR() function'],
            ['Formula Error: Wrong number of arguments for BITOR() function', 1234],
        ];
    }

    /**
     * @dataProvider providerBitOrArray
     */
    public function testBitOrArray(array $expectedResult, string $number1, string $number2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=BITOR({$number1}, {$number2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerBitOrArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [7, 11, 11],
                    [7, 12, 13],
                    [7, 13, 13],
                ],
                '{7, 8, 9}',
                '{3; 4; 5}',
            ],
        ];
    }
}

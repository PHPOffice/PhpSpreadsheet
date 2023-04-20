<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\BitWise;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\TestCase;

class BitAndTest extends TestCase
{
    /**
     * @dataProvider providerBITAND
     *
     * @param mixed $expectedResult
     */
    public function testDirectCallToBITAND($expectedResult, ...$args): void
    {
        $result = BitWise::BITAND(...$args);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider providerBITAND
     *
     * @param mixed $expectedResult
     */
    public function testBITANDAsFormula($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=BITAND({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider providerBITAND
     *
     * @param mixed $expectedResult
     */
    public function testBITANDInWorksheet($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=BITAND({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerBITAND(): array
    {
        return require 'tests/data/Calculation/Engineering/BITAND.php';
    }

    /**
     * @dataProvider providerUnhappyBITAND
     */
    public function testBITANDUnhappyPath(string $expectedException, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=BITAND({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyBITAND(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for BITAND() function'],
            ['Formula Error: Wrong number of arguments for BITAND() function', 1234],
        ];
    }

    /**
     * @dataProvider providerBitAndArray
     */
    public function testBitAndArray(array $expectedResult, string $number1, string $number2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=BITAND({$number1}, {$number2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerBitAndArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [3, 0, 1],
                    [4, 0, 0],
                    [5, 0, 1],
                ],
                '{7, 8, 9}',
                '{3; 4; 5}',
            ],
        ];
    }
}

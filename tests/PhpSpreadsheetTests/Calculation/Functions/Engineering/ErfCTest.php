<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ErfC;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\TestCase;

class ErfCTest extends TestCase
{
    const ERF_PRECISION = 1E-14;

    /**
     * @dataProvider providerERFC
     *
     * @param mixed $expectedResult
     */
    public function testDirectCallToERFC($expectedResult, ...$args): void
    {
        /** @scrutinizer ignore-call */
        $result = ErfC::ERFC(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, self::ERF_PRECISION);
    }

    /**
     * @dataProvider providerERFC
     *
     * @param mixed $expectedResult
     */
    public function testERFCAsFormula($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=ERFC({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, self::ERF_PRECISION);
    }

    /**
     * @dataProvider providerERFC
     *
     * @param mixed $expectedResult
     */
    public function testERFCInWorksheet($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=ERFC({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, self::ERF_PRECISION);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerERFC(): array
    {
        return require 'tests/data/Calculation/Engineering/ERFC.php';
    }

    /**
     * @dataProvider providerUnhappyERFC
     */
    public function testERFCUnhappyPath(string $expectedException, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=ERFC({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyERFC(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for ERFC() function'],
        ];
    }

    /**
     * @dataProvider providerErfCArray
     */
    public function testErfCArray(array $expectedResult, string $lower): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ERFC({$lower})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, self::ERF_PRECISION);
    }

    public static function providerErfCArray(): array
    {
        return [
            'row vector' => [
                [
                    [1.9103139782296354, 1.5204998778130465, 1.0, 0.7236736098317631, 0.0004069520174449588],
                ],
                '{-1.2, -0.5, 0.0, 0.25, 2.5}',
            ],
        ];
    }
}

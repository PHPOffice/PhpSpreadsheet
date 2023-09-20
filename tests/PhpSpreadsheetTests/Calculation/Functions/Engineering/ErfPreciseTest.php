<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\Erf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\TestCase;

class ErfPreciseTest extends TestCase
{
    const ERF_PRECISION = 1E-14;

    /**
     * @dataProvider providerERFPRECISE
     */
    public function testDirectCallToERFPRECISE(mixed $expectedResult, mixed ...$args): void
    {
        $result = Erf::ERFPRECISE(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, self::ERF_PRECISION);
    }

    /**
     * @dataProvider providerERFPRECISE
     */
    public function testERFPRECISEAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=ERF.PRECISE({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, self::ERF_PRECISION);
    }

    /**
     * @dataProvider providerERFPRECISE
     */
    public function testERFPRECISEInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=ERF.PRECISE({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, self::ERF_PRECISION);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerERFPRECISE(): array
    {
        return require 'tests/data/Calculation/Engineering/ERFPRECISE.php';
    }

    /**
     * @dataProvider providerErfPreciseArray
     */
    public function testErfPreciseArray(array $expectedResult, string $limit): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ERF.PRECISE({$limit})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, self::ERF_PRECISION);
    }

    public static function providerErfPreciseArray(): array
    {
        return [
            'row vector' => [
                [
                    [-0.9103139782296353, -0.5204998778130465, 0.0, 0.2763263901682369, 0.999593047982555],
                ],
                '{-1.2, -0.5, 0.0, 0.25, 2.5}',
            ],
        ];
    }
}

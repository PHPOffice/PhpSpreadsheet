<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PhpOffice\PhpSpreadsheetTests\Custom\ComplexAssert;
use PHPUnit\Framework\Attributes\DataProvider;

class ImLog2Test extends ComplexAssert
{
    protected float $complexPrecision = 1E-8;

    #[DataProvider('providerIMLOG2')]
    public function testDirectCallToIMLOG2(string $expectedResult, string $arg): void
    {
        $result = ComplexFunctions::IMLOG2($arg);
        $this->assertComplexEquals($expectedResult, $result);
    }

    #[DataProvider('providerIMLOG2')]
    public function testIMLOG2AsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=IMLOG2({$arguments})";

        /** @var float|int|string */
        $result = $calculation->calculateFormula($formula);
        $this->assertComplexEquals($expectedResult, $result);
    }

    #[DataProvider('providerIMLOG2')]
    public function testIMLOG2InWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMLOG2({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        $this->assertComplexEquals($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerIMLOG2(): array
    {
        return require 'tests/data/Calculation/Engineering/IMLOG2.php';
    }

    #[DataProvider('providerUnhappyIMLOG2')]
    public function testIMLOG2UnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMLOG2({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyIMLOG2(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for IMLOG2() function'],
        ];
    }

    #[DataProvider('providerImLog2Array')]
    public function testImLog2Array(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMLOG2({$complex})";
        $result = $calculation->calculateFormula($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerImLog2Array(): array
    {
        return [
            'row/column vector' => [
                [
                    ['1.4289904975638-2.8151347342002i', '1.3219280948874-2.2661800709136i', '1.4289904975638-1.717225407627i'],
                    ['0.5-3.3992701063704i', '-2.2661800709136i', '0.5-1.1330900354568i'],
                    ['0.5+3.3992701063704i', '2.2661800709136i', '0.5+1.1330900354568i'],
                    ['1.4289904975638+2.8151347342002i', '1.3219280948874+2.2661800709136i', '1.4289904975638+1.717225407627i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}

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

class ImLog10Test extends ComplexAssert
{
    #[DataProvider('providerIMLOG10')]
    public function testDirectCallToIMLOG10(string $expectedResult, string $arg): void
    {
        $result = ComplexFunctions::IMLOG10($arg);
        $this->assertComplexEquals($expectedResult, $result);
    }

    #[DataProvider('providerIMLOG10')]
    public function testIMLOG10AsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=IMLOG10({$arguments})";

        /** @var float|int|string */
        $result = $calculation->calculateFormula($formula);
        $this->assertComplexEquals($expectedResult, $result);
    }

    #[DataProvider('providerIMLOG10')]
    public function testIMLOG10InWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMLOG10({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        $this->assertComplexEquals($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerIMLOG10(): array
    {
        return require 'tests/data/Calculation/Engineering/IMLOG10.php';
    }

    #[DataProvider('providerUnhappyIMLOG10')]
    public function testIMLOG10UnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMLOG10({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyIMLOG10(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for IMLOG10() function'],
        ];
    }

    #[DataProvider('providerImLog10Array')]
    public function testImLog10Array(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMLOG10({$complex})";
        $result = $calculation->calculateFormula($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerImLog10Array(): array
    {
        return [
            'row/column vector' => [
                [
                    ['0.4301690032855-0.84743999682982i', '0.39794000867204-0.68218817692092i', '0.4301690032855-0.51693635701202i'],
                    ['0.15051499783199-1.0232822653814i', '-0.68218817692092i', '0.15051499783199-0.34109408846046i'],
                    ['0.15051499783199+1.0232822653814i', '0.68218817692092i', '0.15051499783199+0.34109408846046i'],
                    ['0.4301690032855+0.84743999682982i', '0.39794000867204+0.68218817692092i', '0.4301690032855+0.51693635701202i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}

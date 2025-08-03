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

class ImCschTest extends ComplexAssert
{
    #[DataProvider('providerIMCSCH')]
    public function testDirectCallToIMCSCH(float|string $expectedResult, string $arg): void
    {
        $result = ComplexFunctions::IMCSCH($arg);
        $this->assertComplexEquals($expectedResult, $result);
    }

    #[DataProvider('providerIMCSCH')]
    public function testIMCSCHAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=IMCSCH({$arguments})";

        /** @var float|int|string */
        $result = $calculation->calculateFormula($formula);
        $this->assertComplexEquals($expectedResult, $result);
    }

    #[DataProvider('providerIMCSCH')]
    public function testIMCSCHInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMCSCH({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        $this->assertComplexEquals($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerIMCSCH(): array
    {
        return require 'tests/data/Calculation/Engineering/IMCSCH.php';
    }

    #[DataProvider('providerUnhappyIMCSCH')]
    public function testIMCSCHUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMCSCH({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyIMCSCH(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for IMCSCH() function'],
        ];
    }

    #[DataProvider('providerImCschArray')]
    public function testImCschArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMCSCH({$complex})";
        $result = $calculation->calculateFormula($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerImCschArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['0.54132290619+0.53096557762117i', '1.6709215455587i', '-0.54132290619+0.53096557762117i'],
                    ['-0.30393100162843+0.62151801717043i', '1.1883951057781i', '0.30393100162843+0.62151801717043i'],
                    ['-0.30393100162843-0.62151801717043i', '-1.1883951057781i', '0.30393100162843-0.62151801717043i'],
                    ['0.54132290619-0.53096557762117i', '-1.6709215455587i', '-0.54132290619-0.53096557762117i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}

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

class ImSechTest extends ComplexAssert
{
    #[DataProvider('providerIMSECH')]
    public function testDirectCallToIMSECH(string $expectedResult, string $arg): void
    {
        $result = ComplexFunctions::IMSECH($arg);
        $this->assertComplexEquals($expectedResult, $result);
    }

    #[DataProvider('providerIMSECH')]
    public function testIMSECHAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=IMSECH({$arguments})";

        /** @var float|int|string */
        $result = $calculation->calculateFormula($formula);
        $this->assertComplexEquals($expectedResult, $result);
    }

    #[DataProvider('providerIMSECH')]
    public function testIMSECHInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMSECH({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        $this->assertComplexEquals($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerIMSECH(): array
    {
        return require 'tests/data/Calculation/Engineering/IMSECH.php';
    }

    #[DataProvider('providerUnhappyIMSECH')]
    public function testIMSECHUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMSECH({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyIMSECH(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for IMSECH() function'],
        ];
    }

    #[DataProvider('providerImSecHArray')]
    public function testImSecHArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMSECH({$complex})";
        $result = $calculation->calculateFormula($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerImSecHArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['-0.61110856415523-0.3476766607105i', '-1.2482156514688', '-0.61110856415523+0.3476766607105i'],
                    ['0.49833703055519-0.59108384172105i', '1.8508157176809', '0.49833703055519+0.59108384172105i'],
                    ['0.49833703055519+0.59108384172105i', '1.8508157176809', '0.49833703055519-0.59108384172105i'],
                    ['-0.61110856415523+0.3476766607105i', '-1.2482156514688', '-0.61110856415523-0.3476766607105i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}

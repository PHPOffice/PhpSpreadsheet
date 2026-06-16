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

class ImCotTest extends ComplexAssert
{
    #[DataProvider('providerIMCOT')]
    public function testDirectCallToIMCOT(float|string $expectedResult, string $arg): void
    {
        $result = ComplexFunctions::IMCOT($arg);
        $this->assertComplexEquals($expectedResult, $result);
    }

    #[DataProvider('providerIMCOT')]
    public function testIMCOTAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=IMCOT({$arguments})";

        /** @var float|int|string */
        $result = $calculation->calculateFormula($formula);
        $this->assertComplexEquals($expectedResult, $result);
    }

    #[DataProvider('providerIMCOT')]
    public function testIMCOTInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMCOT({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        $this->assertComplexEquals($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerIMCOT(): array
    {
        return require 'tests/data/Calculation/Engineering/IMCOT.php';
    }

    #[DataProvider('providerUnhappyIMCOT')]
    public function testIMCOTUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMCOT({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyIMCOT(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for IMCOT() function'],
        ];
    }

    #[DataProvider('providerImCotArray')]
    public function testImCotArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMCOT({$complex})";
        $result = $calculation->calculateFormula($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerImCotArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['-0.012184711291981+0.99433328540776i', '1.0135673098126i', '0.012184711291981+0.99433328540776i'],
                    ['-0.2176215618544+0.86801414289593i', '1.3130352854993i', '0.2176215618544+0.86801414289593i'],
                    ['-0.2176215618544-0.86801414289593i', '-1.3130352854993i', '0.2176215618544-0.86801414289593i'],
                    ['-0.012184711291981-0.99433328540776i', '-1.0135673098126i', '0.012184711291981-0.99433328540776i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}

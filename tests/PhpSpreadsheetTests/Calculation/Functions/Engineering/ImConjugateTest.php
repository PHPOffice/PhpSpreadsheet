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

class ImConjugateTest extends ComplexAssert
{
    #[DataProvider('providerIMCONJUGATE')]
    public function testDirectCallToIMCONJUGATE(string $expectedResult, string $arg): void
    {
        $result = ComplexFunctions::IMCONJUGATE($arg);
        $this->assertComplexEquals($expectedResult, $result);
    }

    #[DataProvider('providerIMCONJUGATE')]
    public function testIMCONJUGATEAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=IMCONJUGATE({$arguments})";

        /** @var float|int|string */
        $result = $calculation->calculateFormula($formula);
        $this->assertComplexEquals($expectedResult, $result);
    }

    #[DataProvider('providerIMCONJUGATE')]
    public function testIMCONJUGATEInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMCONJUGATE({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        $this->assertComplexEquals($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerIMCONJUGATE(): array
    {
        return require 'tests/data/Calculation/Engineering/IMCONJUGATE.php';
    }

    #[DataProvider('providerUnhappyIMCONJUGATE')]
    public function testIMCONJUGATEUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMCONJUGATE({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyIMCONJUGATE(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for IMCONJUGATE() function'],
        ];
    }

    #[DataProvider('providerImConjugateArray')]
    public function testImConjugateArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMCONJUGATE({$complex})";
        $result = $calculation->calculateFormula($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerImConjugateArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['-1+2.5i', '2.5i', '1+2.5i'],
                    ['-1+i', 'i', '1+i'],
                    ['-1-i', '-i', '1-i'],
                    ['-1-2.5i', '-2.5i', '1-2.5i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}

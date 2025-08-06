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

class ImSinTest extends ComplexAssert
{
    #[DataProvider('providerIMSIN')]
    public function testDirectCallToIMSIN(string $expectedResult, string $arg): void
    {
        $result = ComplexFunctions::IMSIN($arg);
        $this->assertComplexEquals($expectedResult, $result);
    }

    #[DataProvider('providerIMSIN')]
    public function testIMSINAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=IMSIN({$arguments})";

        /** @var float|int|string */
        $result = $calculation->calculateFormula($formula);
        $this->assertComplexEquals($expectedResult, $result);
    }

    #[DataProvider('providerIMSIN')]
    public function testIMSINInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMSIN({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        $this->assertComplexEquals($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerIMSIN(): array
    {
        return require 'tests/data/Calculation/Engineering/IMSIN.php';
    }

    #[DataProvider('providerUnhappyIMSIN')]
    public function testIMSINUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMSIN({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyIMSIN(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for IMSIN() function'],
        ];
    }

    #[DataProvider('providerImSinArray')]
    public function testImSinArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMSIN({$complex})";
        $result = $calculation->calculateFormula($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerImSinArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['-5.1601436675797-3.2689394320795i', '-6.0502044810398i', '5.1601436675797-3.2689394320795i'],
                    ['-1.298457581416-0.63496391478474i', '-1.1752011936438i', '1.298457581416-0.63496391478474i'],
                    ['-1.298457581416+0.63496391478474i', '1.1752011936438i', '1.298457581416+0.63496391478474i'],
                    ['-5.1601436675797+3.2689394320795i', '6.0502044810398i', '5.1601436675797+3.2689394320795i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}

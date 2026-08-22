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

class ImSinhTest extends ComplexAssert
{
    #[DataProvider('providerIMSINH')]
    public function testDirectCallToIMSINH(string $expectedResult, string $arg): void
    {
        $result = ComplexFunctions::IMSINH($arg);
        $this->assertComplexEquals($expectedResult, $result);
    }

    #[DataProvider('providerIMSINH')]
    public function testIMSINHAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=IMSINH({$arguments})";

        /** @var float|int|string */
        $result = $calculation->calculateFormula($formula);
        $this->assertComplexEquals($expectedResult, $result);
    }

    #[DataProvider('providerIMSINH')]
    public function testIMSINHInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMSINH({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        $this->assertComplexEquals($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerIMSINH(): array
    {
        return require 'tests/data/Calculation/Engineering/IMSINH.php';
    }

    #[DataProvider('providerUnhappyIMSINH')]
    public function testIMSINHUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMSINH({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyIMSINH(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for IMSINH() function'],
        ];
    }

    #[DataProvider('providerImSinHArray')]
    public function testImSinHArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMSINH({$complex})";
        $result = $calculation->calculateFormula($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerImSinHArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['0.94150493327087-0.92349077604317i', '-0.59847214410396i', '-0.94150493327087-0.92349077604317i'],
                    ['-0.63496391478474-1.298457581416i', '-0.8414709848079i', '0.63496391478474-1.298457581416i'],
                    ['-0.63496391478474+1.298457581416i', '0.8414709848079i', '0.63496391478474+1.298457581416i'],
                    ['0.94150493327087+0.92349077604317i', '0.59847214410396i', '-0.94150493327087+0.92349077604317i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}

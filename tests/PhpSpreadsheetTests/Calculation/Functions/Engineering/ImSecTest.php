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

class ImSecTest extends ComplexAssert
{
    #[DataProvider('providerIMSEC')]
    public function testDirectCallToIMSEC(string $expectedResult, string $arg): void
    {
        $result = ComplexFunctions::IMSEC($arg);
        $this->assertComplexEquals($expectedResult, $result);
    }

    #[DataProvider('providerIMSEC')]
    public function testIMSECAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=IMSEC({$arguments})";

        /** @var float|int|string */
        $result = $calculation->calculateFormula($formula);
        $this->assertComplexEquals($expectedResult, $result);
    }

    #[DataProvider('providerIMSEC')]
    public function testIMSECInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMSEC({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        $this->assertComplexEquals($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerIMSEC(): array
    {
        return require 'tests/data/Calculation/Engineering/IMSEC.php';
    }

    #[DataProvider('providerUnhappyIMSEC')]
    public function testIMSECUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMSEC({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyIMSEC(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for IMSEC() function'],
        ];
    }

    #[DataProvider('providerImSecArray')]
    public function testImSecArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMSEC({$complex})";
        $result = $calculation->calculateFormula($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerImSecArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['0.089798602872122+0.13798100670997i', '0.16307123192998', '0.089798602872122-0.13798100670997i'],
                    ['0.49833703055519+0.59108384172105i', '0.64805427366389', '0.49833703055519-0.59108384172105i'],
                    ['0.49833703055519-0.59108384172105i', '0.64805427366389', '0.49833703055519+0.59108384172105i'],
                    ['0.089798602872122-0.13798100670997i', '0.16307123192998', '0.089798602872122+0.13798100670997i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}

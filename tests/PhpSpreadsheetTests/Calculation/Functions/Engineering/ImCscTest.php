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

class ImCscTest extends ComplexAssert
{
    #[DataProvider('providerIMCSC')]
    public function testDirectCallToIMCSC(float|string $expectedResult, string $arg): void
    {
        $result = ComplexFunctions::IMCSC($arg);
        $this->assertComplexEquals($expectedResult, $result);
    }

    #[DataProvider('providerIMCSC')]
    public function testIMCSCAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=IMCSC({$arguments})";

        /** @var float|int|string */
        $result = $calculation->calculateFormula($formula);
        $this->assertComplexEquals($expectedResult, $result);
    }

    #[DataProvider('providerIMCSC')]
    public function testIMCSCInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMCSC({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        $this->assertComplexEquals($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerIMCSC(): array
    {
        return require 'tests/data/Calculation/Engineering/IMCSC.php';
    }

    #[DataProvider('providerUnhappyIMCSC')]
    public function testIMCSCUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMCSC({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyIMCSC(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for IMCSC() function'],
        ];
    }

    /** @param string[][] $expectedResult */
    #[DataProvider('providerImCscArray')]
    public function testImCscArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMCSC({$complex})";
        /** @var array<string, array<string, string>> */
        $result = $calculation->calculateFormula($formula);
        // Avoid testing for excess precision
        foreach ($expectedResult as &$array) {
            foreach ($array as &$string) {
                $string = preg_replace('/(\d{8})\d+/', '$1', $string);
            }
        }
        foreach ($result as &$array) {
            foreach ($array as &$string) {
                $string = preg_replace('/(\d{8})\d+/', '$1', $string);
            }
        }

        self::assertEquals($expectedResult, $result);
    }

    public static function providerImCscArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['-0.13829327777622+0.087608481088326i', '0.1652836698551i', '0.13829327777622+0.087608481088326i'],
                    ['-0.62151801717043+0.30393100162843i', '0.85091812823932i', '0.62151801717043+0.30393100162843i'],
                    ['-0.62151801717043-0.30393100162843i', '-0.85091812823932i', '0.62151801717043-0.30393100162843i'],
                    ['-0.13829327777622-0.087608481088326i', '-0.1652836698551i', '0.13829327777622-0.087608481088326i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}

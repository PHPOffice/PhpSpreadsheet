<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\Complex;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ComplexTest extends TestCase
{
    #[DataProvider('providerCOMPLEX')]
    public function testDirectCallToCOMPLEX(mixed $expectedResult, mixed ...$args): void
    {
        $result = Complex::complex(...$args);
        self::assertSame($expectedResult, $result);
    }

    #[DataProvider('providerCOMPLEX')]
    public function testCOMPLEXAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=COMPLEX({$arguments})";

        /** @var float|int|string */
        $result = $calculation->calculateFormula($formula);
        self::assertSame($expectedResult, $result);
    }

    #[DataProvider('providerCOMPLEX')]
    public function testCOMPLEXInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=COMPLEX({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerCOMPLEX(): array
    {
        return require 'tests/data/Calculation/Engineering/COMPLEX.php';
    }

    /** @param mixed[] $expectedResult */
    #[DataProvider('providerComplexArray')]
    public function testComplexArray(array $expectedResult, string $real, string $imaginary): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=COMPLEX({$real}, {$imaginary})";
        $result = $calculation->calculateFormula($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerComplexArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['-2.5-2.5i', '-1-2.5i', '-2.5i', '1-2.5i', '2.5-2.5i'],
                    ['-2.5-i', '-1-i', '-i', '1-i', '2.5-i'],
                    ['-2.5', '-1', '0.0', '1', '2.5'],
                    ['-2.5+i', '-1+i', 'i', '1+i', '2.5+i'],
                    ['-2.5+2.5i', '-1+2.5i', '2.5i', '1+2.5i', '2.5+2.5i'],
                ],
                '{-2.5, -1, 0, 1, 2.5}',
                '{-2.5; -1; 0; 1; 2.5}',
            ],
        ];
    }
}

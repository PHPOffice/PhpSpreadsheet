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

class ImPowerTest extends ComplexAssert
{
    #[DataProvider('providerIMPOWER')]
    public function testDirectCallToIMPOWER(float|int|string $expectedResult, string $arg1, float|int|string $arg2): void
    {
        $result = ComplexFunctions::IMPOWER($arg1, $arg2);
        $this->assertComplexEquals($expectedResult, $result);
    }

    #[DataProvider('providerIMPOWER')]
    public function testIMPOWERAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=IMPOWER({$arguments})";

        /** @var float|int|string */
        $result = $calculation->calculateFormula($formula);
        $this->assertComplexEquals($expectedResult, $result);
    }

    #[DataProvider('providerIMPOWER')]
    public function testIMPOWERInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMPOWER({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        $this->assertComplexEquals($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerIMPOWER(): array
    {
        return require 'tests/data/Calculation/Engineering/IMPOWER.php';
    }

    #[DataProvider('providerUnhappyIMPOWER')]
    public function testIMPOWERUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMPOWER({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyIMPOWER(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for IMPOWER() function'],
        ];
    }

    #[DataProvider('providerImPowerArray')]
    public function testImPowerArray(array $expectedResult, string $complex, string $real): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMPOWER({$complex}, {$real})";
        $result = $calculation->calculateFormula($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerImPowerArray(): array
    {
        return [
            'matrix' => [
                [
                    ['-5.25+5i', '-2.8702659355016E-15+15.625i', '2.5625+52.5i'],
                    ['-3.6739403974421E-16+2i', '-1.836970198721E-16+i', '-4-4.8985871965894E-16i'],
                    ['-3.6739403974421E-16-2i', '-1.836970198721E-16-i', '-4+4.8985871965894E-16i'],
                    ['-5.25-5i', '-2.8702659355016E-15-15.625i', '2.5625-52.5i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
                '{2, 3, 4}',
            ],
        ];
    }
}

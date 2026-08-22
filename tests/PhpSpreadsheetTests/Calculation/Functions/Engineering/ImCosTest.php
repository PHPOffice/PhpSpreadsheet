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

class ImCosTest extends ComplexAssert
{
    #[DataProvider('providerIMCOS')]
    public function testDirectCallToIMCOS(string $expectedResult, string $arg): void
    {
        $result = ComplexFunctions::IMCOS($arg);
        $this->assertComplexEquals($expectedResult, $result);
    }

    #[DataProvider('providerIMCOS')]
    public function testIMCOSAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=IMCOS({$arguments})";

        /** @var float|int|string */
        $result = $calculation->calculateFormula($formula);
        $this->assertComplexEquals($expectedResult, $result);
    }

    #[DataProvider('providerIMCOS')]
    public function testIMCOSInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMCOS({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        $this->assertComplexEquals($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerIMCOS(): array
    {
        return require 'tests/data/Calculation/Engineering/IMCOS.php';
    }

    #[DataProvider('providerUnhappyIMCOS')]
    public function testIMCOSUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMCOS({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyIMCOS(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for IMCOS() function'],
        ];
    }

    #[DataProvider('providerImCosArray')]
    public function testImCosArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMCOS({$complex})";
        $result = $calculation->calculateFormula($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerImCosArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['3.3132901461132-5.0910715229497i', 6.1322894796637, '3.3132901461132+5.0910715229497i'],
                    ['0.83373002513115-0.98889770576287i', 1.5430806348152, '0.83373002513115+0.98889770576287i'],
                    ['0.83373002513115+0.98889770576287i', 1.5430806348152, '0.83373002513115-0.98889770576287i'],
                    ['3.3132901461132+5.0910715229497i', 6.1322894796637, '3.3132901461132-5.0910715229497i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}

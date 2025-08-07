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

class ImSqrtTest extends ComplexAssert
{
    #[DataProvider('providerIMSQRT')]
    public function testDirectCallToIMSQRT(string $expectedResult, string $arg): void
    {
        $result = ComplexFunctions::IMSQRT($arg);
        $this->assertComplexEquals($expectedResult, $result);
    }

    #[DataProvider('providerIMSQRT')]
    public function testIMSQRTAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=IMSQRT({$arguments})";

        /** @var float|int|string */
        $result = $calculation->calculateFormula($formula);
        $this->assertComplexEquals($expectedResult, $result);
    }

    #[DataProvider('providerIMSQRT')]
    public function testIMSQRTInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMSQRT({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        $this->assertComplexEquals($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerIMSQRT(): array
    {
        return require 'tests/data/Calculation/Engineering/IMSQRT.php';
    }

    #[DataProvider('providerUnhappyIMSQRT')]
    public function testIMSQRTUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMSQRT({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyIMSQRT(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for IMSQRT() function'],
        ];
    }

    /** @param mixed[] $expectedResult */
    #[DataProvider('providerImSqrtArray')]
    public function testImSqrtArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMSQRT({$complex})";
        $result = $calculation->calculateFormula($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerImSqrtArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['0.9199408686343-1.3587829855366i', '1.1180339887499-1.1180339887499i', '1.3587829855366-0.9199408686343i'],
                    ['0.45508986056223-1.0986841134678i', '0.70710678118655-0.70710678118655i', '1.0986841134678-0.45508986056223i'],
                    ['0.45508986056223+1.0986841134678i', '0.70710678118655+0.70710678118655i', '1.0986841134678+0.45508986056223i'],
                    ['0.9199408686343+1.3587829855366i', '1.1180339887499+1.1180339887499i', '1.3587829855366+0.9199408686343i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}

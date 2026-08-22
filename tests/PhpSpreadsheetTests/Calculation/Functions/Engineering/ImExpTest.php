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

class ImExpTest extends ComplexAssert
{
    protected float $complexPrecision = (PHP_INT_SIZE > 4) ? 1E-12 : 1E-9;

    #[DataProvider('providerIMEXP')]
    public function testDirectCallToIMEXP(string $expectedResult, string $arg): void
    {
        $result = ComplexFunctions::IMEXP($arg);
        $this->assertComplexEquals($expectedResult, $result);
    }

    #[DataProvider('providerIMEXP')]
    public function testIMEXPAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=IMEXP({$arguments})";

        /** @var float|int|string */
        $result = $calculation->calculateFormula($formula);
        $this->assertComplexEquals($expectedResult, $result);
    }

    #[DataProvider('providerIMEXP')]
    public function testIMEXPInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMEXP({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        $this->assertComplexEquals($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerIMEXP(): array
    {
        return require 'tests/data/Calculation/Engineering/IMEXP.php';
    }

    #[DataProvider('providerUnhappyIMEXP')]
    public function testIMEXPUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMEXP({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyIMEXP(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for IMEXP() function'],
        ];
    }

    #[DataProvider('providerImExpArray')]
    public function testImExpArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMEXP({$complex})";
        $result = $calculation->calculateFormula($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerImExpArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['-0.29472426558547-0.22016559792964i', '-0.80114361554693-0.59847214410396i', '-2.1777341321272-1.6268159541567i'],
                    ['0.19876611034641-0.30955987565311i', '0.54030230586814-0.8414709848079i', '1.4686939399159-2.2873552871788i'],
                    ['0.19876611034641+0.30955987565311i', '0.54030230586814+0.8414709848079i', '1.4686939399159+2.2873552871788i'],
                    ['-0.29472426558547+0.22016559792964i', '-0.80114361554693+0.59847214410396i', '-2.1777341321272+1.6268159541567i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}

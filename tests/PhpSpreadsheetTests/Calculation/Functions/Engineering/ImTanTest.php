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

class ImTanTest extends ComplexAssert
{
    #[DataProvider('providerIMTAN')]
    public function testDirectCallToIMTAN(string $expectedResult, string $arg): void
    {
        $result = ComplexFunctions::IMTAN($arg);
        $this->assertComplexEquals($expectedResult, $result);
    }

    #[DataProvider('providerIMTAN')]
    public function testIMTANAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=IMTAN({$arguments})";

        /** @var float|int|string */
        $result = $calculation->calculateFormula($formula);
        $this->assertComplexEquals($expectedResult, $result);
    }

    #[DataProvider('providerIMTAN')]
    public function testIMTANInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMTAN({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        $this->assertComplexEquals($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerIMTAN(): array
    {
        return require 'tests/data/Calculation/Engineering/IMTAN.php';
    }

    #[DataProvider('providerUnhappyIMTAN')]
    public function testIMTANUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMTAN({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyIMTAN(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for IMTAN() function'],
        ];
    }

    /** @param mixed[] $expectedResult */
    #[DataProvider('providerImTanArray')]
    public function testImTanArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMTAN({$complex})";
        $result = $calculation->calculateFormula($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerImTanArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['-0.012322138255828-1.0055480118951i', '-0.98661429815143i', '0.012322138255828-1.0055480118951i'],
                    ['-0.27175258531951-1.0839233273387i', '-0.76159415595576i', '0.27175258531951-1.0839233273387i'],
                    ['-0.27175258531951+1.0839233273387i', '0.76159415595576i', '0.27175258531951+1.0839233273387i'],
                    ['-0.012322138255828+1.0055480118951i', '0.98661429815143i', '0.012322138255828+1.0055480118951i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}

<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\Attributes\DataProvider;

class SumTest extends AllSetupTeardown
{
    #[DataProvider('providerSUM')]
    public function testSUM(mixed $expectedResult, mixed ...$args): void
    {
        $this->setArrayAsValue();
        $sheet = $this->getSheet();
        $row = 0;
        foreach ($args as $arg) {
            ++$row;
            $sheet->getCell("A$row")->setValue($arg);
        }
        $sheet->getCell('B1')->setValue("=SUM(A1:A$row)");
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public static function providerSUM(): array
    {
        return require 'tests/data/Calculation/MathTrig/SUM.php';
    }

    #[DataProvider('providerSUMLiterals')]
    public function testSUMLiterals(mixed $expectedResult, string $args): void
    {
        $sheet = $this->getSheet();
        $sheet->getCell('B1')->setValue("=SUM($args)");
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public static function providerSUMLiterals(): array
    {
        return require 'tests/data/Calculation/MathTrig/SUMLITERALS.php';
    }

    #[DataProvider('providerSUMCellArguments')]
    public function testSUMCellArguments(mixed $expectedResult, string $formula, string $arrayReturnType): void
    {
        $spreadsheet = $this->getSpreadsheet();
        Calculation::getInstance($spreadsheet)->setInstanceArrayReturnType($arrayReturnType);
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('N/A');
        $sheet->getCell('A2')->setValue(5);
        $sheet->getCell('A3')->setValue(7);
        $other = $spreadsheet->createSheet();
        $other->setTitle('Other');
        $other->getCell('A1')->setValue('text');
        $sheet->getCell('B1')->setValue($formula);
        self::assertSame($expectedResult, $sheet->getCell('B1')->getCalculatedValue());
    }

    public static function providerSUMCellArguments(): array
    {
        $formulas = [
            'text in a single-cell reference is ignored' => [5, '=SUM(A1,A2)'],
            'text in the only argument is ignored' => [0, '=SUM(A1)'],
            'text in a reference to another sheet is ignored' => [12, '=SUM(Other!A1,A2,A3)'],
            'text in a range is ignored' => [12, '=SUM(A1:A3)'],
            'text typed as an argument is an error' => [ExcelError::VALUE(), '=SUM("N/A",A2)'],
        ];
        $cases = [];
        foreach ([Calculation::RETURN_ARRAY_AS_VALUE, Calculation::RETURN_ARRAY_AS_ARRAY] as $arrayReturnType) {
            foreach ($formulas as $name => [$expectedResult, $formula]) {
                $cases["$name, $arrayReturnType"] = [$expectedResult, $formula, $arrayReturnType];
            }
        }

        return $cases;
    }

    #[DataProvider('providerSUMWITHINDEXMATCH')]
    public function testSumWithIndexMatch(mixed $expectedResult, string $formula): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $this->setArrayAsValue();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Formula');
        $sheet1->fromArray(
            [
                ['Number', 'Formula'],
                [83, $formula],
            ]
        );
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Lookup');
        $sheet2->fromArray(
            [
                ['Lookup', 'Match'],
                [83, 16],
            ]
        );
        self::assertSame($expectedResult, $sheet1->getCell('B2')->getCalculatedValue());
    }

    public static function providerSUMWITHINDEXMATCH(): array
    {
        return require 'tests/data/Calculation/MathTrig/SUMWITHINDEXMATCH.php';
    }

    public function testSumWithIndexMatchMoved(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Formula');
        $sheet1->getCell('G5')->setValue('Number');
        $sheet1->getCell('H5')->setValue('Formula');
        $sheet1->getCell('G6')->setValue(83);
        $sheet1->getCell('H6')->setValue('=SUM(4 * INDEX(Lookup!D4:D6, MATCH(G6, Lookup!C4:C6, 0)))');
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Lookup');
        $sheet2->getCell('C3')->setValue('Lookup');
        $sheet2->getCell('D3')->setValue('Match');
        $sheet2->getCell('C4')->setValue(82);
        $sheet2->getCell('D4')->setValue(15);
        $sheet2->getCell('C5')->setValue(83);
        $sheet2->getCell('D5')->setValue(16);
        $sheet2->getCell('C6')->setValue(84);
        $sheet2->getCell('D6')->setValue(17);
        self::assertSame(64, $sheet1->getCell('H6')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }
}

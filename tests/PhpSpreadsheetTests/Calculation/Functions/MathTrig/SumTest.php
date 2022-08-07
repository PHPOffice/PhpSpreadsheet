<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class SumTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSUM
     *
     * @param mixed $expectedResult
     */
    public function testSUM($expectedResult, ...$args): void
    {
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

    public function providerSUM(): array
    {
        return require 'tests/data/Calculation/MathTrig/SUM.php';
    }

    /**
     * @dataProvider providerSUMLiterals
     *
     * @param mixed $expectedResult
     */
    public function testSUMLiterals($expectedResult, string $args): void
    {
        $sheet = $this->getSheet();
        $sheet->getCell('B1')->setValue("=SUM($args)");
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerSUMLiterals(): array
    {
        return require 'tests/data/Calculation/MathTrig/SUMLITERALS.php';
    }

    /**
     * @dataProvider providerSUMWITHINDEXMATCH
     *
     * @param mixed $expectedResult
     */
    public function testSumWithIndexMatch($expectedResult, string $formula): void
    {
        $spreadsheet = new Spreadsheet();
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
        $spreadsheet->disconnectWorksheets();
    }

    public function providerSUMWITHINDEXMATCH(): array
    {
        return require 'tests/data/Calculation/MathTrig/SUMWITHINDEXMATCH.php';
    }
}

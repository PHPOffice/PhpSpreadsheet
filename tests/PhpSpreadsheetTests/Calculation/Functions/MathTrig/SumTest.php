<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class SumTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSUM
     */
    public function testSUM(mixed $expectedResult, mixed ...$args): void
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

    public static function providerSUM(): array
    {
        return require 'tests/data/Calculation/MathTrig/SUM.php';
    }

    /**
     * @dataProvider providerSUMLiterals
     */
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

    /**
     * @dataProvider providerSUMWITHINDEXMATCH
     */
    public function testSumWithIndexMatch(mixed $expectedResult, string $formula): void
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

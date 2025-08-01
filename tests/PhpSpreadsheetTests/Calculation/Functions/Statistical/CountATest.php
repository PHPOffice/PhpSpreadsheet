<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class CountATest extends AllSetupTeardown
{
    #[\PHPUnit\Framework\Attributes\DataProvider('providerCOUNTA')]
    public function testCOUNTA(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCases('COUNTA', $expectedResult, ...$args);
    }

    public static function providerCOUNTA(): array
    {
        return require 'tests/data/Calculation/Statistical/COUNTA.php';
    }

    public function testNull(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', '=COUNTA(B1,B2,B3,B4)');
        $sheet->setCellValue('A2', '=COUNTA(B1,,B3,B4)');
        $sheet->setCellValue('A3', '=COUNTA(B1,B2,B3,B4)');
        self::assertSame(0, $sheet->getCell('A1')->getCalculatedValue(), 'empty cells not counted');
        self::assertSame(1, $sheet->getCell('A2')->getCalculatedValue(), 'null argument is counted');
        self::assertSame(0, $sheet->getCell('A3')->getCalculatedValue(), 'empty cells still not counted');
        $spreadsheet->disconnectWorksheets();
    }
}

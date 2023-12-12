<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class RefRangeTest extends TestCase
{
    /**
     * @dataProvider providerRefRange
     */
    public function testRefRange(int|string $expectedResult, string $rangeString): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue("=SUM($rangeString)");
        self::assertSame($expectedResult, $sheet->getCell('A1')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }

    public static function providerRefRange(): array
    {
        return [
            'normal range' => [0, 'B1:B2'],
            'ref as end of range' => ['#REF!', 'B1:#REF!'],
            'ref as start of range' => ['#REF!', '#REF!:B2'],
            'ref as both parts of range' => ['#REF!', '#REF!:#REF!'],
            'using indirect for ref' => ['#REF!', 'B1:INDIRECT("XYZ")'],
        ];
    }

    public function testRefRangeRead(): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load('tests/data/Reader/XLSX/issue.3453.xlsx');
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame(0, $sheet->getCell('H1')->getCalculatedValue());
        self::assertSame('#REF!', $sheet->getCell('H2')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }
}

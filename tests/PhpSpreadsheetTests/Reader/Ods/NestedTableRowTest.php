<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Reader\Ods as OdsReader;
use PHPUnit\Framework\TestCase;

class NestedTableRowTest extends TestCase
{
    public function testTableHeaderRows(): void
    {
        $infile = 'tests/data/Reader/Ods/issue.4528.ods';
        $reader = new OdsReader();
        $spreadsheet = $reader->load($infile);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('Atterissage', $sheet->getCell('AS1')->getValue());
        self::assertNull($sheet->getCell('AS2')->getValue());
        self::assertSame('jour', $sheet->getCell('AS3')->getValue());
        self::assertSame('=SUM(Y3:INDIRECT(CONCATENATE("Y",$C$3)))', $sheet->getCell('AS4')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testTableRowGroup(): void
    {
        $infile = 'tests/data/Reader/Ods/issue.2507.ods';
        $reader = new OdsReader();
        $spreadsheet = $reader->load($infile);
        $sheet = $spreadsheet->getActiveSheet();
        $values = $sheet->rangeToArray('B3:C7', null, false, false);
        $expected = [
            ['Номенклатура', "Складское наличие,\nКол-во"], // before table-row-group
            ['Квадрат 140х140мм ст.5ХНМ (т)', 0.225], // within table-row-group
            ['Квадрат 200х200мм ст.3 (т)', 1.700],
            ['Квадрат 210х210мм ст.65Г (т)', 0.280],
            ['Квадрат 250х250мм ст.45 (т)', 0.133],
        ];
        self::assertSame($expected, $values);
        $spreadsheet->disconnectWorksheets();
    }
}

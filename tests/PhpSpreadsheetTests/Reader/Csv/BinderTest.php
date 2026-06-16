<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Csv;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PHPUnit\Framework\TestCase;

class BinderTest extends TestCase
{
    public function testLoadFromString(): void
    {
        $data = <<<EOF
            1,2,3
            4,5,6
            EOF;
        $reader1 = new Csv();
        $spreadsheet1 = $reader1->loadSpreadsheetFromString($data);
        $sheet1 = $spreadsheet1->getActiveSheet();
        $sheet1->getCell('A3')->setValueExplicit(7, DataType::TYPE_STRING);
        $sheet1->getCell('B3')->setValueExplicit(8, DataType::TYPE_NUMERIC);
        $sheet1->setCellValue('C3', 9);
        $sheet1->fromArray([10, 11, 12], null, 'A4');
        $expected1 = [
            [1, 2, 3],
            [4, 5, 6],
            ['7', 8, 9],
            [10, 11, 12],
        ];
        self::AssertSame($expected1, $sheet1->toArray(null, false, false));

        $reader2 = new Csv();
        $reader2->setValueBinder(new StringValueBinder());
        self::assertInstanceOf(StringValueBinder::class, $reader2->getValueBinder());
        $spreadsheet2 = $reader2->loadSpreadsheetFromString($data);
        $sheet2 = $spreadsheet2->getActiveSheet();
        $sheet2->getCell('A3')->setValueExplicit(7, DataType::TYPE_STRING);
        $sheet2->getCell('B3')->setValueExplicit(8, DataType::TYPE_NUMERIC);
        $sheet2->setCellValue('C3', 9);
        $sheet2->fromArray([10, 11, 12], null, 'A4');
        $expected2 = [
            ['1', '2', '3'],
            ['4', '5', '6'],
            ['7', 8, '9'],
            ['10', '11', '12'],
        ];
        self::AssertSame($expected2, $sheet2->toArray(null, false, false));

        $spreadsheet1->disconnectWorksheets();
        $spreadsheet2->disconnectWorksheets();
    }
}

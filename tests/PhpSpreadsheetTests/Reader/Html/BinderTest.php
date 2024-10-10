<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Html;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PHPUnit\Framework\TestCase;

class BinderTest extends TestCase
{
    public function testLoadFromString(): void
    {
        $data = <<<EOF
            <table>
            <tbody>
            <tr><td>1</td><td>2</td><td>3</td></tr>
            <tr><td>4</td><td>5</td><td>6</td></tr>
            </tbody>
            </table>
            EOF;
        $reader1 = new Html();
        $spreadsheet1 = $reader1->loadFromString($data);
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

        $reader2 = new Html();
        $reader2->setValueBinder(new StringValueBinder());
        $spreadsheet2 = $reader2->loadFromString($data);
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

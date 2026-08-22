<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;

/**
 * Many of these tests are derived from
 * https://exceljet.net/functions/vstack-function.
 */
class VStackTest extends AllSetupTeardown
{
    public static function testVstack1(): void
    {
        $spreadsheet = new Spreadsheet();
        Calculation::getInstance($spreadsheet)
            ->setInstanceArrayReturnType(
                Calculation::RETURN_ARRAY_AS_ARRAY
            );
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B2', 'Array1');
        $sheet->setCellValue('B3', 'Red');
        $sheet->setCellValue('B4', 'Blue');
        $sheet->setCellValue('B5', 'Green');
        $sheet->setCellValue('B7', 'Array2');
        $sheet->setCellValue('B8', 'Green');
        $sheet->setCellValue('B9', 'Red');
        $sheet->setCellValue('D2', 'Result');
        $sheet->setCellValue('D3', '=VSTACK(B3:B5,B8:B9)');
        $expected = [
            ['Red'],
            ['Blue'],
            ['Green'],
            ['Green'],
            ['Red'],
        ];
        self::assertSame($expected, $sheet->getCell('D3')->getCalculatedValue());

        $sheet->setCellValue('F2', '=VSTACK(B2,B8:B9)');
        $expected = [
            ['Array1'],
            ['Green'],
            ['Red'],
        ];
        self::assertSame($expected, $sheet->getCell('F2')->getCalculatedValue(), 'one single-cell argument');

        $spreadsheet->disconnectWorksheets();
    }

    public static function testVstack2(): void
    {
        $spreadsheet = new Spreadsheet();
        Calculation::getInstance($spreadsheet)
            ->setInstanceArrayReturnType(
                Calculation::RETURN_ARRAY_AS_ARRAY
            );
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B3', 'Red');
        $sheet->setCellValue('B4', 'Blue');
        $sheet->setCellValue('B5', 'Green');
        $sheet->setCellValue('B6', 'Blue');
        $sheet->setCellValue('B7', 'Red');
        $sheet->setCellValue('C3', 12);
        $sheet->setCellValue('C4', 9);
        $sheet->setCellValue('C5', 10);
        $sheet->setCellValue('C5', 10);
        $sheet->setCellValue('C6', 11);
        $sheet->setCellValue('C7', 8);
        $sheet->setCellValue('E3', '=VSTACK({"Color","Qty"},B3:C7)');
        $expected = [
            ['Color', 'Qty'],
            ['Red', 12],
            ['Blue', 9],
            ['Green', 10],
            ['Blue', 11],
            ['Red', 8],
        ];
        self::assertSame($expected, $sheet->getCell('E3')->getCalculatedValue());
        $sheet->setCellValue('A1', 'Purple');
        $sheet->setCellValue('A2', 'Orange');
        $sheet->setCellValue('H3', '=VSTACK({"Color","Qty"},A1:A2,B3:C7)');
        $expected = [
            ['Color', 'Qty'],
            ['Purple', '#N/A'],
            ['Orange', '#N/A'],
            ['Red', 12],
            ['Blue', 9],
            ['Green', 10],
            ['Blue', 11],
            ['Red', 8],
        ];
        self::assertSame($expected, $sheet->getCell('H3')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Excel has a problem here.
     * If it reads VSTACK(Table1, Table2) without square
     * brackets after Table1/2, it calculates the result as #NAME?.
     * You can then just edit the formula without making
     * any changes, and it will calculate it correctly,
     * but it will add in the brackets when saving.
     * This seems pretty buggy to me.
     * PhpSpreadsheet will handle the formula with or without
     * the brackets, but you should specify them to avoid
     * problems with Excel.
     */
    public static function testTables(): void
    {
        $spreadsheet = new Spreadsheet();
        Calculation::getInstance($spreadsheet)
            ->setInstanceArrayReturnType(
                Calculation::RETURN_ARRAY_AS_ARRAY
            );
        $sheet = $spreadsheet->getActiveSheet();

        $data = [
            ['Date', 'Color', 'Qty'],
            [Date::stringToExcel('2021-04-03'), 'Red', 12],
            [Date::stringToExcel('2021-04-07'), 'Blue', 9],
            [Date::stringToExcel('2021-04-11'), 'Green', 10],
            [Date::stringToExcel('2021-04-15'), 'Blue', 11],
            [Date::stringToExcel('2021-04-20'), 'Red', 8],
        ];
        $sheet->fromArray($data, null, 'B4', true);
        $table = new Table('B4:D9', 'Table1');
        $sheet->addTable($table);

        $data = [
            ['Date', 'Color', 'Qty'],
            [Date::stringToExcel('2021-05-05'), 'Red', 12],
            [Date::stringToExcel('2021-05-12'), 'Blue', 9],
            [Date::stringToExcel('2021-05-18'), 'Green', 10],
            [Date::stringToExcel('2021-05-21'), 'Blue', 11],
            [Date::stringToExcel('2021-05-28'), 'Green', 6],
        ];
        $sheet->fromArray($data, null, 'B11', true);
        $table = new Table('B11:D16', 'Table2');
        $sheet->addTable($table);

        $sheet->setCellValue('G4', 'Date');
        $sheet->setCellValue('H4', 'Color');
        $sheet->setCellValue('I4', 'Qty');
        $sheet->setCellValue('G5', '=VSTACK(Table1[],Table2[])');
        $sheet->getCell('G5')->getCalculatedValue();

        $sheet->getStyle('B4:B16')
            ->getNumberFormat()
            ->setFormatCode('d-mmm');
        $sheet->getStyle('G5:G14')
            ->getNumberFormat()
            ->setFormatCode('d-mmm');
        $expected = [
            ['3-Apr', 'Red', '12'],
            ['7-Apr', 'Blue', '9'],
            ['11-Apr', 'Green', '10'],
            ['15-Apr', 'Blue', '11'],
            ['20-Apr', 'Red', '8'],
            ['5-May', 'Red', '12'],
            ['12-May', 'Blue', '9'],
            ['18-May', 'Green', '10'],
            ['21-May', 'Blue', '11'],
            ['28-May', 'Green', '6'],
        ];
        $actual = $sheet->rangeToArray('G5:I14', null, true, true);
        self::assertSame($expected, $actual);

        $spreadsheet->disconnectWorksheets();
    }
}

<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class CyclicTest extends TestCase
{
    public function testCyclicReference(): void
    {
        // Issue 3169
        $spreadsheet = new Spreadsheet();

        $table_data = [
            ['abc', 'def', 'ghi'],
            ['1', '4', '=B3+A3'],
            ['=SUM(A2:C2)', '2', '=A2+B2'],
        ];
        // Don't allow cyclic references.
        Calculation::getInstance($spreadsheet)->cyclicFormulaCount = 0;

        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->fromArray($table_data, '');

        try {
            $result = $worksheet->getCell('C2')->getCalculatedValue();
        } catch (CalcException $e) {
            $result = $e->getMessage();
        }
        self::assertSame(
            'Worksheet!C2 -> Worksheet!A3 -> Worksheet!C2 -> Cyclic Reference in Formula',
            $result
        );

        try {
            $result = $worksheet->getCell('A3')->getCalculatedValue();
        } catch (CalcException $e) {
            $result = $e->getMessage();
        }
        self::assertSame(
            'Worksheet!A3 -> Worksheet!C2 -> Worksheet!A3 -> Cyclic Reference in Formula',
            $result
        );
        $spreadsheet->disconnectWorksheets();
    }
}

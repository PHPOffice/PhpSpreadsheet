<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class ReferenceHelper4Test extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testIssue3907(string $expectedResult, string $settingsTitle, string $formula, string $dataTitle = 'DATA'): void
    {
        $spreadsheet = new Spreadsheet();
        $dataSheet = $spreadsheet->getActiveSheet();
        $dataSheet->setTitle($dataTitle);
        $settingsSheet = $spreadsheet->createSheet();
        $settingsSheet->setTitle($settingsTitle);
        $settingsSheet->getCell('A1')->setValue(10);
        $settingsSheet->getCell('B1')->setValue(20);
        $dataSheet->getCell('A5')->setValue($formula);
        $dataSheet->getCell('A2')->setValue(1);
        $dataSheet->insertNewColumnBefore('A');
        self::assertSame($expectedResult, $dataSheet->getCell('B5')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public static function dataProvider(): array
    {
        return [
            ["=SUM(B2, 'F1 (SETTINGS)'!A1:B1)", 'F1 (SETTINGS)', "=SUM(A2, 'F1 (SETTINGS)'!A1:B1)"],
            ["=SUM(B2, 'x F1 (SETTINGS)'!A1:B1)", 'x F1 (SETTINGS)', "=SUM(A2, 'x F1 (SETTINGS)'!A1:B1)"],
            ["=SUM(B2, 'DATA'!B1)", 'F1 (SETTINGS)', "=SUM(A2, 'DATA'!A1)"],
            ["=SUM(B2, 'DATA'!C1)", 'F1 (SETTINGS)', "=SUM(A2, 'data'!B1)"],
            ['=SUM(B2, DATA!D1)', 'F1 (SETTINGS)', '=SUM(A2, data!C1)'],
            ["=SUM(B2, 'DATA'!B1:C1)", 'F1 (SETTINGS)', "=SUM(A2, 'Data'!A1:B1)"],
            ['=SUM(B2, DATA!B1:C1)', 'F1 (SETTINGS)', '=SUM(A2, DAta!A1:B1)'],
            ["=SUM(B2, 'F1 Data'!C1)", 'F1 (SETTINGS)', "=SUM(A2, 'F1 Data'!B1)", 'F1 Data'],
            ["=SUM(B2, 'x F1 Data'!C1)", 'F1 (SETTINGS)', "=SUM(A2, 'x F1 Data'!B1)", 'x F1 Data'],
            ["=SUM(B2, 'x F1 Data'!C1)", 'F1 (SETTINGS)', "=SUM(A2, 'x F1 Data'!B1)", 'x F1 Data'],
            ["=SUM(B2, 'x F1 Data'!C1:D2)", 'F1 (SETTINGS)', "=SUM(A2, 'x F1 Data'!B1:C2)", 'x F1 Data'],
            ['=SUM(B2, definedname1A1)', 'F1 (SETTINGS)', '=SUM(A2, definedname1A1)', 'x F1 Data'],
        ];
    }
}

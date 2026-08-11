<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

class Issue4542Test extends TestCase
{
    public function testXmlSpace(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $string = ' Ye&ar ';
        $trimString = trim($string);
        $sheet->getCell('A1')->setValue($string);
        $sheet->getCell('A2')->setValueExplicit($string, DataType::TYPE_INLINE);
        $sheet->getCell('B1')->setValue($trimString);
        $sheet->getCell('B2')->setValueExplicit($trimString, DataType::TYPE_INLINE);
        $writer = new XlsxWriter($spreadsheet);

        $writer->createStyleDictionaries();
        $writerStyle = new XlsxWriter\Style($writer);
        $data = $writerStyle->writeStyles($spreadsheet);
        self::assertStringContainsString(
            '<styleSheet',
            $data
        );
        self::assertStringNotContainsString(
            'xml:space',
            $data
        );

        $writerWorkbook = new XlsxWriter\Workbook($writer);
        $data = $writerWorkbook->writeWorkbook($spreadsheet);
        self::assertStringContainsString(
            '<workbook',
            $data
        );
        self::assertStringNotContainsString(
            'xml:space',
            $data
        );

        $stringTable = $writer->createStringTable();
        $writerStringTable = new XlsxWriter\StringTable($writer);
        $data = $writerStringTable->writeStringTable($stringTable);
        self::assertStringContainsString(
            '<si><t xml:space="preserve"> Ye&amp;ar </t></si>',
            $data
        );
        self::assertStringContainsString(
            '<si><t>Ye&amp;ar</t></si>',
            $data
        );

        $writerWorksheet = new XlsxWriter\Worksheet($writer);
        $data = $writerWorksheet->writeWorksheet($sheet, []);
        self::assertStringContainsString(
            '<c r="A2" t="inlineStr"><is><t xml:space="preserve"> Ye&amp;ar </t></is></c>',
            $data
        );
        self::assertStringContainsString(
            '<c r="B2" t="inlineStr"><is><t>Ye&amp;ar</t></is></c>',
            $data
        );

        $spreadsheet->disconnectWorksheets();
    }

    public function testTable(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(
            [
                ['MyCol', 'Colonne2', 'Colonne3'],
                [10, 20],
                [2],
                [3],
                [4],
            ],
            null,
            'B1',
            true
        );
        $table = new Table('B1:D5', 'Tableau1');
        $sheet->addTable($table);

        $writer = new XlsxWriter($spreadsheet);
        $writerTable = new XlsxWriter\Table($writer);
        $data = $writerTable->writeTable($table, 1);

        self::assertStringContainsString(
            '<table ',
            $data
        );
        self::assertStringNotContainsString(
            'xml:space',
            $data
        );
        $spreadsheet->disconnectWorksheets();
    }
}

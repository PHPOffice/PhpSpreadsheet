<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xml;

use PhpOffice\PhpSpreadsheet\Reader\Xml;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BadDateTest extends TestCase
{
    #[DataProvider('providerXmlSpreadsheet')]
    public function testXml(string $filename): void
    {
        $reader = new Xml();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getSheetByNameOrThrow('Sample Data');
        self::assertSame('1960-12-19', $sheet->getCell('A10')->getFormattedValue());
        self::assertSame('1.50', $sheet->getCell('A11')->getFormattedValue());
        self::assertSame('2:30 AM', $sheet->getCell('A13')->getFormattedValue());
        self::assertSame('1960-12-19 01:30', $sheet->getCell('A15')->getFormattedValue());
        $sheet = $spreadsheet->getSheetByNameOrThrow('Report Data');
        self::assertSame('11-Nov-2011', $sheet->getCell('D27')->getFormattedValue());
        self::assertSame('11:00 AM', $sheet->getCell('E27')->getFormattedValue());
        self::assertSame('12-Dec-2012', $sheet->getCell('D28')->getFormattedValue());
        self::assertSame('1899-12-31T288:00:00.000', $sheet->getCell('E28')->getValue(), 'invalid date/time-stamp treated as plain string');
        $spreadsheet->disconnectWorksheets();
    }

    /** @return string[][] */
    public static function providerXmlSpreadsheet(): array
    {
        return [
            'spreadsheet with bad datetimestamp and formulas' => ['tests/data/Reader/Xml/Excel2003XMLTest.xml'],
            'spreadsheet with formulas corrected' => ['tests/data/Reader/Xml/Excel2003XMLTest.corrected.xml'],
        ];
    }

    public function testFormats(): void
    {
        $reader = new Xml();
        $reader
            ->setNumberFormatMapping('Currency', '¥#,##0;[Red](¥#,##0)')
            ->setNumberFormatMapping('Yes/No', '"Oui";"Oui";"Non"')
            ->setNumberFormatMapping('On/Off', '"Marche";"Marche";"Arrêt"');
        $filename = 'tests/data/Reader/Xml/formats.xml';
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        $expected = [
            ['True/False', 'True', 'False'],
            ['Yes/No', 'Oui', 'Non'],
            ['On/Off', 'Marche', 'Arrêt'],
            ['Long Time', '6:00:00 PM', null],
            ['Percent', '97.00%', null],
            ['Currency', '(¥1,235)', null],
            ['Standard', '1,234.57', null],
            ['Fixed', '1234.58', null],
            ['Euro Currency', '(€1,234.59)', null],
            ['Scientific', '-1.23E+3', null],
        ];
        self::assertSame($expected, $sheet->toArray());
        $spreadsheet->disconnectWorksheets();
    }

    public function testUtf16(): void
    {
        $reader = new Xml();
        $filename = 'tests/data/Reader/Xml/formats.utf16lebom.xml';
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        $expected = [
            ['True/False', 'True', 'False'],
            ['Yes/No', 'Yes', 'No'],
            ['On/Off', 'On', 'Off'],
            ['Long Time', '6:00:00 PM', null],
            ['Percent', '97.00%', null],
            ['Currency', '($1,234.56)', null],
            ['Standard', '1,234.57', null],
            ['Fixed', '1234.58', null],
            ['Euro Currency', '(€1,234.59)', null],
            ['Scientific', '-1.23E+3', null],
        ];
        self::assertSame($expected, $sheet->toArray());
        $spreadsheet->disconnectWorksheets();
    }
}

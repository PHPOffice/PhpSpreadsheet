<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xml;

use DateTimeImmutable;
use PhpOffice\PhpSpreadsheet\Reader\Xml;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class XmlPropertiesTest extends AbstractFunctional
{
    private string $filename = 'tests/data/Reader/Xml/hyperlinkbase.xml';

    public function testProperties(): void
    {
        $reader = new Xml();
        $spreadsheet = $reader->load($this->filename);

        $properties = $spreadsheet->getProperties();
        self::assertSame('title', $properties->getTitle());
        self::assertSame('topic', $properties->getSubject());
        self::assertSame('author', $properties->getCreator());
        self::assertSame('keyword1, keyword2', $properties->getKeywords());
        self::assertSame('no comment', $properties->getDescription());
        self::assertSame('last author', $properties->getLastModifiedBy());
        $expected = self::timestampToInt('2023-05-18T11:21:43Z');
        self::assertEquals($expected, $properties->getCreated());
        $expected = self::timestampToInt('2023-05-18T11:30:00Z');
        self::assertEquals($expected, $properties->getModified());
        self::assertSame('category', $properties->getCategory());
        self::assertSame('manager', $properties->getManager());
        self::assertSame('company', $properties->getCompany());

        self::assertSame('https://phpspreadsheet.readthedocs.io/en/latest/', $properties->getHyperlinkBase());

        self::assertSame('TheString', $properties->getCustomPropertyValue('StringProperty'));
        self::assertSame(12345, $properties->getCustomPropertyValue('NumberProperty'));
        $expected = self::timestampToInt('2023-05-18T10:00:00Z');
        self::assertEquals($expected, $properties->getCustomPropertyValue('DateProperty'));
        $expected = self::timestampToInt('2023-05-19T11:00:00Z');
        self::assertEquals($expected, $properties->getCustomPropertyValue('DateProperty2'));
        self::assertTrue($properties->getCustomPropertyValue('BooleanPropertyTrue'));
        self::assertFalse($properties->getCustomPropertyValue('BooleanPropertyFalse'));
        self::assertEqualsWithDelta(1.2345, $properties->getCustomPropertyValue('FloatProperty'), 1E-8);

        $sheet = $spreadsheet->getActiveSheet();
        // Note that relative links don't actually work in XML format.
        // It will, however, work just fine in the Xlsx and Html copies.
        $hyperlink = $sheet->getCell('A1')->getHyperlink();
        self::assertSame('references/features-cross-reference/', $hyperlink->getUrl());
        // Same comment as for cell above.
        self::assertSame('topics/accessing-cells/', $sheet->getCell('A2')->getCalculatedValue());
        // No problem for absolute links.
        $hyperlink = $sheet->getCell('A3')->getHyperlink();
        self::assertSame('https://www.google.com/', $hyperlink->getUrl());
        self::assertSame('https://www.yahoo.com', $sheet->getCell('A4')->getCalculatedValue());

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();

        $properties = $reloadedSpreadsheet->getProperties();
        self::assertSame('title', $properties->getTitle());
        self::assertSame('topic', $properties->getSubject());
        self::assertSame('author', $properties->getCreator());
        self::assertSame('keyword1, keyword2', $properties->getKeywords());
        self::assertSame('no comment', $properties->getDescription());
        self::assertSame('last author', $properties->getLastModifiedBy());
        $expected = self::timestampToInt('2023-05-18T11:21:43Z');
        self::assertEquals($expected, $properties->getCreated());
        $expected = self::timestampToInt('2023-05-18T11:30:00Z');
        self::assertEquals($expected, $properties->getModified());
        self::assertSame('category', $properties->getCategory());
        self::assertSame('manager', $properties->getManager());
        self::assertSame('company', $properties->getCompany());

        self::assertSame('https://phpspreadsheet.readthedocs.io/en/latest/', $properties->getHyperlinkBase());

        self::assertSame('TheString', $properties->getCustomPropertyValue('StringProperty'));
        self::assertSame(12345, $properties->getCustomPropertyValue('NumberProperty'));
        // Note that Xlsx will ignore the time part when displaying
        // the property.
        $expected = self::timestampToInt('2023-05-18T10:00:00Z');
        self::assertEquals($expected, $properties->getCustomPropertyValue('DateProperty'));
        $expected = self::timestampToInt('2023-05-19T11:00:00Z');
        self::assertEquals($expected, $properties->getCustomPropertyValue('DateProperty2'));
        self::assertTrue($properties->getCustomPropertyValue('BooleanPropertyTrue'));
        self::assertFalse($properties->getCustomPropertyValue('BooleanPropertyFalse'));
        self::assertEqualsWithDelta(1.2345, $properties->getCustomPropertyValue('FloatProperty'), 1E-8);

        $sheet = $reloadedSpreadsheet->getActiveSheet();
        // Note that relative links don't actually work in XML format.
        // It will, however, work just fine in the Xlsx and Html copies.
        $hyperlink = $sheet->getCell('A1')->getHyperlink();
        self::assertSame('references/features-cross-reference/', $hyperlink->getUrl());
        // Same comment as for cell above.
        self::assertSame('topics/accessing-cells/', $sheet->getCell('A2')->getCalculatedValue());
        // No problem for absolute links.
        $hyperlink = $sheet->getCell('A3')->getHyperlink();
        self::assertSame('https://www.google.com/', $hyperlink->getUrl());
        self::assertSame('https://www.yahoo.com', $sheet->getCell('A4')->getCalculatedValue());

        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testPropertiesHtml(): void
    {
        $reader = new Xml();
        $spreadsheet = $reader->load($this->filename);

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Html');
        $spreadsheet->disconnectWorksheets();

        $properties = $reloadedSpreadsheet->getProperties();
        self::assertSame('https://phpspreadsheet.readthedocs.io/en/latest/', $properties->getHyperlinkBase());

        self::assertSame('title', $properties->getTitle());
        self::assertSame('topic', $properties->getSubject());
        self::assertSame('author', $properties->getCreator());
        self::assertSame('keyword1, keyword2', $properties->getKeywords());
        self::assertSame('no comment', $properties->getDescription());
        self::assertSame('last author', $properties->getLastModifiedBy());
        $expected = self::timestampToInt('2023-05-18T11:21:43Z');
        self::assertEquals($expected, $properties->getCreated());
        $expected = self::timestampToInt('2023-05-18T11:30:00Z');
        self::assertEquals($expected, $properties->getModified());
        self::assertSame('category', $properties->getCategory());
        self::assertSame('manager', $properties->getManager());
        self::assertSame('company', $properties->getCompany());

        self::assertSame('TheString', $properties->getCustomPropertyValue('StringProperty'));
        self::assertSame(12345, $properties->getCustomPropertyValue('NumberProperty'));
        $expected = self::timestampToInt('2023-05-18T10:00:00Z');
        self::assertEquals($expected, $properties->getCustomPropertyValue('DateProperty'));
        $expected = self::timestampToInt('2023-05-19T11:00:00Z');
        self::assertEquals($expected, $properties->getCustomPropertyValue('DateProperty2'));
        self::assertTrue($properties->getCustomPropertyValue('BooleanPropertyTrue'));
        self::assertFalse($properties->getCustomPropertyValue('BooleanPropertyFalse'));
        self::assertEqualsWithDelta(1.2345, $properties->getCustomPropertyValue('FloatProperty'), 1E-8);

        $sheet = $reloadedSpreadsheet->getActiveSheet();
        // Note that relative links don't actually work in XML format.
        // It will, however, work just fine in the Xlsx and Html copies.
        $hyperlink = $sheet->getCell('A1')->getHyperlink();
        self::assertSame('references/features-cross-reference/', $hyperlink->getUrl());
        // Same comment as for cell above.
        self::assertSame('topics/accessing-cells/', $sheet->getCell('A2')->getCalculatedValue());
        // No problem for absolute links.
        $hyperlink = $sheet->getCell('A3')->getHyperlink();
        self::assertSame('https://www.google.com/', $hyperlink->getUrl());
        self::assertSame('https://www.yahoo.com', $sheet->getCell('A4')->getCalculatedValue());

        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testHyperlinksXls(): void
    {
        $reader = new Xml();
        $spreadsheet = $reader->load($this->filename);

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xls');
        $spreadsheet->disconnectWorksheets();

        $sheet = $reloadedSpreadsheet->getActiveSheet();
        // Note that relative links don't actually work in XML format.
        // However, Xls Writer will convert relative to absolute.
        $hyperlink = $sheet->getCell('A1')->getHyperlink();
        self::assertSame('https://phpspreadsheet.readthedocs.io/en/latest/references/features-cross-reference/', $hyperlink->getUrl());
        // Xls writer does not get involved in function call.
        // However, hyperlink does get updated somewhere.
        //self::assertSame('topics/accessing-cells/', $sheet->getCell('A2')->getCalculatedValue());
        $hyperlink = $sheet->getCell('A2')->getHyperlink();
        self::assertSame('https://phpspreadsheet.readthedocs.io/en/latest/topics/accessing-cells/', $hyperlink->getUrl());
        // No problem for absolute links.
        $hyperlink = $sheet->getCell('A3')->getHyperlink();
        self::assertSame('https://www.google.com/', $hyperlink->getUrl());
        self::assertSame('https://www.yahoo.com', $sheet->getCell('A4')->getCalculatedValue());

        $reloadedSpreadsheet->disconnectWorksheets();
    }

    private static function timestampToInt(string $timestamp): string
    {
        $dto = new DateTimeImmutable($timestamp);

        return $dto->format('U');
    }
}

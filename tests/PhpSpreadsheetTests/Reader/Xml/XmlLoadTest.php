<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xml;

use DateTimeZone;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Reader\Xml;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class XmlLoadTest extends TestCase
{
    private ?Spreadsheet $spreadsheet = null;

    private string $locale;

    protected function setUp(): void
    {
        $this->locale = Settings::getLocale();
    }

    protected function tearDown(): void
    {
        if ($this->spreadsheet !== null) {
            $this->spreadsheet->disconnectWorksheets();
            $this->spreadsheet = null;
        }
        Settings::setLocale($this->locale);
    }

    public function testLoadEnglish(): void
    {
        $this->xtestLoad();
    }

    public function testLoadFrench(): void
    {
        Settings::setLocale('fr');
        $this->xtestLoad();
    }

    public function xtestLoad(): void
    {
        $filename = __DIR__
            . '/../../../..'
            . '/samples/templates/excel2003.xml';
        $reader = new Xml();
        $this->spreadsheet = $spreadsheet = $reader->load($filename);
        self::assertEquals(2, $spreadsheet->getSheetCount());

        $sheet = $spreadsheet->getSheet(1);
        self::assertEquals('Report Data', $sheet->getTitle());
        self::assertEquals('BCD', $sheet->getCell('A4')->getValue());
        $props = $spreadsheet->getProperties();
        self::assertEquals('Mark Baker', $props->getCreator());
        $creationDate = $props->getCreated();
        $result = Date::formattedDateTimeFromTimestamp("$creationDate", 'Y-m-d\\TH:i:s\\Z', new DateTimeZone('UTC'));
        self::assertEquals('2010-09-02T20:48:39Z', $result);
        $creationDate = $props->getModified();
        $result = Date::formattedDateTimeFromTimestamp("$creationDate", 'Y-m-d\\TH:i:s\\Z', new DateTimeZone('UTC'));
        self::assertEquals('2010-09-03T21:48:39Z', $result);
        self::assertEquals('AbCd1234', $props->getCustomPropertyValue('my_API_Token'));
        self::assertEquals('2', $props->getCustomPropertyValue('myאInt'));
        /** @var string */
        $creationDate = $props->getCustomPropertyValue('my_API_Token_Expiry');
        $result = Date::formattedDateTimeFromTimestamp("$creationDate", 'Y-m-d\\TH:i:s\\Z', new DateTimeZone('UTC'));
        self::assertEquals('2019-01-31T07:00:00Z', $result);

        $sheet = $spreadsheet->getSheet(0);
        self::assertEquals('Sample Data', $sheet->getTitle());
        self::assertEquals('Test String 1', $sheet->getCell('A1')->getValue());
        self::assertEquals('Test with (") in string', $sheet->getCell('A4')->getValue());

        self::assertEquals(22269, $sheet->getCell('A10')->getValue());
        self::assertEquals('dd/mm/yyyy', $sheet->getCell('A10')->getStyle()->getNumberFormat()->getFormatCode());
        self::assertEquals('19/12/1960', $sheet->getCell('A10')->getFormattedValue());
        self::assertEquals(1.5, $sheet->getCell('A11')->getValue());
        self::assertEquals('# ?0/??0', $sheet->getCell('A11')->getStyle()->getNumberFormat()->getFormatCode());
        // Same pattern, same value, different display in Gnumeric vs Excel
        //self::assertEquals('1 1/2', $sheet->getCell('A11')->getFormattedValue());
        self::assertEquals('hh":"mm":"ss', $sheet->getCell('A13')->getStyle()->getNumberFormat()->getFormatCode());
        self::assertEquals('02:30:00', $sheet->getCell('A13')->getFormattedValue());
        self::assertEquals('d/m/yy hh":"mm', $sheet->getCell('A15')->getStyle()->getNumberFormat()->getFormatCode());
        self::assertEquals('19/12/60 01:30', $sheet->getCell('A15')->getFormattedValue());

        self::assertEquals('=B1+C1', $sheet->getCell('H1')->getValue());
        self::assertEquals('=E2&F2', $sheet->getCell('J2')->getValue());
        self::assertEquals('=SUM(C1:C4)', $sheet->getCell('I5')->getValue());

        // property not yet supported
        //self::assertFalse($sheet->getRowDimension(30)->getVisible());
        $hyperlink = $sheet->getCell('A21');
        self::assertEquals('PhpSpreadsheet', $hyperlink->getValue());
        self::assertEquals('https://github.com/PHPOffice/PhpSpreadsheet', $hyperlink->getHyperlink()->getUrl());
    }

    public function testLoadFilter(): void
    {
        $filename = __DIR__
            . '/../../../..'
            . '/samples/templates/excel2003.xml';
        $reader = new Xml();
        $filter = new XmlFilter();
        $reader->setReadFilter($filter);
        $this->spreadsheet = $spreadsheet = $reader->load($filename);
        self::assertEquals(2, $spreadsheet->getSheetCount());
        $sheet = $spreadsheet->getSheet(1);
        self::assertEquals('Report Data', $sheet->getTitle());
        self::assertEquals('', $sheet->getCell('A4')->getValue());
        $props = $spreadsheet->getProperties();
        self::assertEquals('Mark Baker', $props->getCreator());
    }

    public function testLoadSelectedSheets(): void
    {
        $filename = __DIR__
            . '/../../../..'
            . '/samples/templates/excel2003.xml';
        $reader = new Xml();
        $reader->setLoadSheetsOnly(['Unknown Sheet', 'Report Data']);
        $this->spreadsheet = $spreadsheet = $reader->load($filename);
        self::assertEquals(1, $spreadsheet->getSheetCount());
        $sheet = $spreadsheet->getSheet(0);
        self::assertEquals('Report Data', $sheet->getTitle());
        self::assertEquals('Third Heading', $sheet->getCell('C2')->getValue());
    }

    public function testLoadNoSelectedSheets(): void
    {
        $this->expectException(PhpSpreadsheetException::class);
        $this->expectExceptionMessage('You tried to set a sheet active by the out of bounds index');
        $filename = __DIR__
            . '/../../../..'
            . '/samples/templates/excel2003.xml';
        $reader = new Xml();
        $reader->setLoadSheetsOnly(['Unknown Sheet', 'xReport Data']);
        $this->spreadsheet = $reader->load($filename);
    }

    public function testLoadUnusableSample(): void
    {
        // Sample spreadsheet is not readable by Excel.
        // But PhpSpreadsheet can load it.
        $filename = __DIR__
            . '/../../../..'
            . '/samples/templates/excel2003.short.bad.xml';
        $reader = new Xml();
        $this->spreadsheet = $spreadsheet = $reader->load($filename);
        self::assertEquals(1, $spreadsheet->getSheetCount());
        $sheet = $spreadsheet->getSheet(0);
        self::assertEquals('Sample Data', $sheet->getTitle());
    }
}

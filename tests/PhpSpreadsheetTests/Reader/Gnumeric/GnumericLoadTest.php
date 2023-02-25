<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Gnumeric;

use DateTimeZone;
use PhpOffice\PhpSpreadsheet\Reader\Gnumeric;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PHPUnit\Framework\TestCase;

class GnumericLoadTest extends TestCase
{
    public function testLoad(): void
    {
        $filename = __DIR__
            . '/../../../..'
            . '/samples/templates/GnumericTest.gnumeric';
        $reader = new Gnumeric();
        $spreadsheet = $reader->load($filename);
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
        self::assertEquals('2020-06-05T05:15:21Z', $result);

        $sheet = $spreadsheet->getSheet(0);
        self::assertEquals('Sample Data', $sheet->getTitle());
        self::assertEquals('Test String 1', $sheet->getCell('A1')->getValue());
        self::assertEquals('FFFF0000', $sheet->getCell('A1')->getStyle()->getFont()->getColor()->getARGB());
        self::assertEquals(Font::UNDERLINE_SINGLE, $sheet->getCell('A3')->getStyle()->getFont()->getUnderline());
        self::assertEquals('Test with (") in string', $sheet->getCell('A4')->getValue());

        self::assertEquals(22269, $sheet->getCell('A10')->getValue());
        self::assertEquals('dd/mm/yyyy', $sheet->getCell('A10')->getStyle()->getNumberFormat()->getFormatCode());
        self::assertEquals('19/12/1960', $sheet->getCell('A10')->getFormattedValue());
        self::assertEquals(1.5, $sheet->getCell('A11')->getValue());
        self::assertEquals('# ?0/??0', $sheet->getCell('A11')->getStyle()->getNumberFormat()->getFormatCode());
        // Same pattern, same value, different display in Gnumeric vs Excel
        //self::assertEquals('1 1/2', $sheet->getCell('A11')->getFormattedValue());

        self::assertEquals('=B1+C1', $sheet->getCell('H1')->getValue());
        self::assertEquals('=E2&F2', $sheet->getCell('J2')->getValue());
        self::assertEquals('=sum(C1:C4)', $sheet->getCell('I5')->getValue());

        self::assertTrue($sheet->getCell('E1')->getStyle()->getFont()->getBold());
        self::assertTrue($sheet->getCell('E1')->getStyle()->getFont()->getItalic());

        self::assertFalse($sheet->getCell('E2')->getStyle()->getFont()->getBold());
        self::assertFalse($sheet->getCell('E2')->getStyle()->getFont()->getItalic());
        self::assertEquals(Font::UNDERLINE_NONE, $sheet->getCell('E2')->getStyle()->getFont()->getUnderline());
        self::assertTrue($sheet->getCell('E3')->getStyle()->getFont()->getBold());
        self::assertFalse($sheet->getCell('E3')->getStyle()->getFont()->getItalic());
        self::assertEquals(Font::UNDERLINE_NONE, $sheet->getCell('E3')->getStyle()->getFont()->getUnderline());
        self::assertFalse($sheet->getCell('E4')->getStyle()->getFont()->getBold());
        self::assertTrue($sheet->getCell('E4')->getStyle()->getFont()->getItalic());
        self::assertEquals(Font::UNDERLINE_NONE, $sheet->getCell('E4')->getStyle()->getFont()->getUnderline());

        self::assertTrue($sheet->getCell('F1')->getStyle()->getFont()->getBold());
        self::assertFalse($sheet->getCell('F1')->getStyle()->getFont()->getItalic());
        self::assertEquals(Font::UNDERLINE_NONE, $sheet->getCell('F1')->getStyle()->getFont()->getUnderline());
        self::assertFalse($sheet->getCell('F2')->getStyle()->getFont()->getBold());
        self::assertFalse($sheet->getCell('F2')->getStyle()->getFont()->getItalic());
        self::assertEquals(Font::UNDERLINE_NONE, $sheet->getCell('F2')->getStyle()->getFont()->getUnderline());
        self::assertTrue($sheet->getCell('F3')->getStyle()->getFont()->getBold());
        self::assertTrue($sheet->getCell('F3')->getStyle()->getFont()->getItalic());
        self::assertEquals(Font::UNDERLINE_NONE, $sheet->getCell('F3')->getStyle()->getFont()->getUnderline());
        self::assertFalse($sheet->getCell('F4')->getStyle()->getFont()->getBold());
        self::assertFalse($sheet->getCell('F4')->getStyle()->getFont()->getItalic());
        self::assertEquals(Font::UNDERLINE_NONE, $sheet->getCell('F4')->getStyle()->getFont()->getUnderline());

        self::assertEquals(Border::BORDER_MEDIUM, $sheet->getCell('C10')->getStyle()->getBorders()->getTop()->getBorderStyle());
        self::assertEquals(Border::BORDER_NONE, $sheet->getCell('C10')->getStyle()->getBorders()->getRight()->getBorderStyle());
        self::assertEquals(Border::BORDER_NONE, $sheet->getCell('C10')->getStyle()->getBorders()->getBottom()->getBorderStyle());
        self::assertEquals(Border::BORDER_NONE, $sheet->getCell('C10')->getStyle()->getBorders()->getLeft()->getBorderStyle());
        self::assertEquals(Border::BORDER_NONE, $sheet->getCell('C12')->getStyle()->getBorders()->getTop()->getBorderStyle());
        self::assertEquals(Border::BORDER_NONE, $sheet->getCell('C12')->getStyle()->getBorders()->getRight()->getBorderStyle());
        self::assertEquals(Border::BORDER_MEDIUM, $sheet->getCell('C12')->getStyle()->getBorders()->getBottom()->getBorderStyle());
        self::assertEquals(Border::BORDER_NONE, $sheet->getCell('C12')->getStyle()->getBorders()->getLeft()->getBorderStyle());
        self::assertEquals(Border::BORDER_NONE, $sheet->getCell('C14')->getStyle()->getBorders()->getTop()->getBorderStyle());
        self::assertEquals(Border::BORDER_NONE, $sheet->getCell('C14')->getStyle()->getBorders()->getRight()->getBorderStyle());
        self::assertEquals(Border::BORDER_NONE, $sheet->getCell('C14')->getStyle()->getBorders()->getBottom()->getBorderStyle());
        self::assertEquals(Border::BORDER_MEDIUM, $sheet->getCell('C14')->getStyle()->getBorders()->getLeft()->getBorderStyle());
        self::assertEquals(Border::BORDER_NONE, $sheet->getCell('C16')->getStyle()->getBorders()->getTop()->getBorderStyle());
        self::assertEquals(Border::BORDER_MEDIUM, $sheet->getCell('C16')->getStyle()->getBorders()->getRight()->getBorderStyle());
        self::assertEquals(Border::BORDER_NONE, $sheet->getCell('C16')->getStyle()->getBorders()->getBottom()->getBorderStyle());
        self::assertEquals(Border::BORDER_NONE, $sheet->getCell('C16')->getStyle()->getBorders()->getLeft()->getBorderStyle());
        self::assertEquals(Border::BORDER_THICK, $sheet->getCell('C18')->getStyle()->getBorders()->getTop()->getBorderStyle());
        self::assertEquals(Color::COLOR_RED, $sheet->getCell('C18')->getStyle()->getBorders()->getTop()->getColor()->getARGB());
        self::assertEquals(Border::BORDER_THICK, $sheet->getCell('C18')->getStyle()->getBorders()->getRight()->getBorderStyle());
        self::assertEquals(Color::COLOR_YELLOW, $sheet->getCell('C18')->getStyle()->getBorders()->getRight()->getColor()->getARGB());
        self::assertEquals(Border::BORDER_THICK, $sheet->getCell('C18')->getStyle()->getBorders()->getRight()->getBorderStyle());
        self::assertEquals(Border::BORDER_NONE, $sheet->getCell('C18')->getStyle()->getBorders()->getBottom()->getBorderStyle());
        self::assertEquals(Border::BORDER_NONE, $sheet->getCell('C18')->getStyle()->getBorders()->getLeft()->getBorderStyle());

        self::assertEquals(Fill::FILL_PATTERN_DARKHORIZONTAL, $sheet->getCell('K19')->getStyle()->getFill()->getFillType());
        self::assertEquals('FF00CCFF', $sheet->getCell('K19')->getStyle()->getFill()->getEndColor()->getARGB());
        self::assertEquals(Color::COLOR_BLUE, $sheet->getCell('K19')->getStyle()->getFill()->getStartColor()->getARGB());
        self::assertEquals(Fill::FILL_PATTERN_GRAY0625, $sheet->getCell('L19')->getStyle()->getFill()->getFillType());
        self::assertEquals(Color::COLOR_RED, $sheet->getCell('L19')->getStyle()->getFill()->getEndColor()->getARGB());
        self::assertEquals(Color::COLOR_YELLOW, $sheet->getCell('L19')->getStyle()->getFill()->getStartColor()->getARGB());
        self::assertEquals(Fill::FILL_SOLID, $sheet->getCell('K3')->getStyle()->getFill()->getFillType());
        self::assertEquals(Color::COLOR_RED, $sheet->getCell('K3')->getStyle()->getFill()->getStartColor()->getARGB());

        self::assertEquals(45, $sheet->getCell('E22')->getStyle()->getAlignment()->getTextRotation());
        self::assertEquals(-90, $sheet->getCell('G22')->getStyle()->getAlignment()->getTextRotation());
        self::assertEquals(Border::BORDER_DOUBLE, $sheet->getCell('N13')->getStyle()->getBorders()->getBottom()->getBorderStyle());

        self::assertEquals(Borders::DIAGONAL_BOTH, $sheet->getCell('E18')->getStyle()->getBorders()->getDiagonalDirection());
        self::assertEquals(Borders::DIAGONAL_DOWN, $sheet->getCell('I18')->getStyle()->getBorders()->getDiagonalDirection());
        self::assertEquals(Borders::DIAGONAL_UP, $sheet->getCell('J18')->getStyle()->getBorders()->getDiagonalDirection());
        self::assertEquals(Font::UNDERLINE_DOUBLE, $sheet->getCell('A24')->getStyle()->getFont()->getUnderline());
        self::assertTrue($sheet->getCell('B23')->getStyle()->getFont()->getSubScript());
        self::assertTrue($sheet->getCell('B24')->getStyle()->getFont()->getSuperScript());
        $rowDimension = $sheet->getRowDimension(30);
        self::assertFalse($rowDimension->getVisible());

        self::assertSame('B24', $sheet->getSelectedCells());
        $spreadsheet->disconnectWorksheets();
    }

    public function testLoadFilter(): void
    {
        $filename = __DIR__
            . '/../../../..'
            . '/samples/templates/GnumericTest.gnumeric';
        $reader = new Gnumeric();
        $filter = new GnumericFilter();
        $reader->setReadFilter($filter);
        $spreadsheet = $reader->load($filename);
        self::assertEquals(2, $spreadsheet->getSheetCount());
        $sheet = $spreadsheet->getSheet(1);
        self::assertEquals('Report Data', $sheet->getTitle());
        self::assertEquals('', $sheet->getCell('A4')->getValue());
        $props = $spreadsheet->getProperties();
        self::assertEquals('Mark Baker', $props->getCreator());
        $spreadsheet->disconnectWorksheets();
    }

    public function testLoadOld(): void
    {
        $filename = __DIR__
            . '/../../../..'
            . '/samples/templates/old.gnumeric';
        $reader = new Gnumeric();
        $spreadsheet = $reader->load($filename);
        $props = $spreadsheet->getProperties();
        self::assertEquals('David Gilbert', $props->getCreator());
        $spreadsheet->disconnectWorksheets();
    }

    public function testLoadSelectedSheets(): void
    {
        $filename = __DIR__
            . '/../../../..'
            . '/samples/templates/GnumericTest.gnumeric';
        $reader = new Gnumeric();
        $reader->setLoadSheetsOnly(['Unknown Sheet', 'Report Data']);
        $spreadsheet = $reader->load($filename);
        self::assertEquals(1, $spreadsheet->getSheetCount());
        $sheet = $spreadsheet->getSheet(0);
        self::assertEquals('Report Data', $sheet->getTitle());
        self::assertEquals('Third Heading', $sheet->getCell('C2')->getValue());

        self::assertSame('A1', $sheet->getSelectedCells());
        $spreadsheet->disconnectWorksheets();
    }
}

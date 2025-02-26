<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xml;

use PhpOffice\PhpSpreadsheet\Reader\Xml;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PHPUnit\Framework\TestCase;

class XmlStylesTest extends TestCase
{
    public function testBorders(): void
    {
        $filename = __DIR__
            . '/../../../..'
            . '/samples/templates/excel2003.xml';
        $reader = new Xml();
        $spreadsheet = $reader->load($filename);

        $sheet = $spreadsheet->getSheet(0);
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

        self::assertEquals(Borders::DIAGONAL_BOTH, $sheet->getCell('E18')->getStyle()->getBorders()->getDiagonalDirection());
        self::assertEquals(Borders::DIAGONAL_DOWN, $sheet->getCell('I18')->getStyle()->getBorders()->getDiagonalDirection());
        self::assertEquals(Borders::DIAGONAL_UP, $sheet->getCell('J18')->getStyle()->getBorders()->getDiagonalDirection());
        $spreadsheet->disconnectWorksheets();
    }

    public function testFont(): void
    {
        $filename = __DIR__
            . '/../../../..'
            . '/samples/templates/excel2003.xml';
        $reader = new Xml();
        $spreadsheet = $reader->load($filename);

        $sheet = $spreadsheet->getSheet(0);
        self::assertEquals('FFFF0000', $sheet->getCell('A1')->getStyle()->getFont()->getColor()->getARGB());
        self::assertEquals(Font::UNDERLINE_SINGLE, $sheet->getCell('A3')->getStyle()->getFont()->getUnderline());

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

        self::assertEquals(45, $sheet->getCell('E22')->getStyle()->getAlignment()->getTextRotation());
        self::assertEquals(-90, $sheet->getCell('G22')->getStyle()->getAlignment()->getTextRotation());
        self::assertEquals(Border::BORDER_DOUBLE, $sheet->getCell('N13')->getStyle()->getBorders()->getBottom()->getBorderStyle());

        self::assertEquals(Font::UNDERLINE_DOUBLE, $sheet->getCell('A24')->getStyle()->getFont()->getUnderline());
        self::assertTrue($sheet->getCell('B23')->getStyle()->getFont()->getSubScript());
        self::assertTrue($sheet->getCell('B24')->getStyle()->getFont()->getSuperScript());
        $spreadsheet->disconnectWorksheets();
    }

    public function testFill(): void
    {
        $filename = __DIR__
            . '/../../../..'
            . '/samples/templates/excel2003.xml';
        $reader = new Xml();
        $spreadsheet = $reader->load($filename);

        $sheet = $spreadsheet->getSheet(0);
        self::assertEquals(Fill::FILL_PATTERN_DARKHORIZONTAL, $sheet->getCell('K19')->getStyle()->getFill()->getFillType());
        self::assertEquals('FF00CCFF', $sheet->getCell('K19')->getStyle()->getFill()->getEndColor()->getARGB());
        self::assertEquals(Color::COLOR_BLUE, $sheet->getCell('K19')->getStyle()->getFill()->getStartColor()->getARGB());
        self::assertEquals(Fill::FILL_PATTERN_GRAY0625, $sheet->getCell('L19')->getStyle()->getFill()->getFillType());
        self::assertEquals(Color::COLOR_RED, $sheet->getCell('L19')->getStyle()->getFill()->getEndColor()->getARGB());
        self::assertEquals(Color::COLOR_YELLOW, $sheet->getCell('L19')->getStyle()->getFill()->getStartColor()->getARGB());
        self::assertEquals(Fill::FILL_SOLID, $sheet->getCell('K3')->getStyle()->getFill()->getFillType());
        self::assertEquals(Color::COLOR_RED, $sheet->getCell('K3')->getStyle()->getFill()->getEndColor()->getARGB());
        $spreadsheet->disconnectWorksheets();
    }

    public function testAlignment(): void
    {
        $filename = __DIR__
            . '/../../../..'
            . '/samples/templates/excel2003.xml';
        $reader = new Xml();
        $spreadsheet = $reader->load($filename);

        $sheet = $spreadsheet->getSheet(0);
        self::assertEquals(45, $sheet->getCell('E22')->getStyle()->getAlignment()->getTextRotation());
        self::assertEquals(-90, $sheet->getCell('G22')->getStyle()->getAlignment()->getTextRotation());
        self::assertEquals(Alignment::HORIZONTAL_CENTER, $sheet->getCell('N2')->getStyle()->getAlignment()->getHorizontal());
        self::assertEquals(Alignment::HORIZONTAL_RIGHT, $sheet->getCell('N3')->getStyle()->getAlignment()->getHorizontal());
        self::assertEquals(Alignment::VERTICAL_TOP, $sheet->getCell('K19')->getStyle()->getAlignment()->getVertical());
        $spreadsheet->disconnectWorksheets();
    }
}

<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xml;

use PhpOffice\PhpSpreadsheet\Helper\Dimension;
use PhpOffice\PhpSpreadsheet\Reader\Xml as XmlReader;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PHPUnit\Framework\TestCase;

class ColspanTest extends TestCase
{
    public static function testColspans1(): void
    {
        $infile = 'tests/data/Reader/Xml/colspans1.xml';
        $reader = new XmlReader();
        $spreadsheet = $reader->load($infile);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('S', $sheet->getHighestColumn());
        self::assertSame('S', $sheet->getHighestDataColumn());
        $expected = [
            'A' => '3.334cm Visible',
            'B' => 'default Visible',
            'C' => 'default Visible',
            'D' => '2.699cm Visible',
            'E' => '2.699cm Visible',
            'F' => '2.699cm Visible',
            'G' => 'default Visible',
            'H' => 'default Hidden',
            'I' => 'default Hidden',
            'J' => 'default Hidden',
            'K' => '2.17cm Hidden',
            'L' => '2.17cm Hidden',
            'M' => '2.17cm Hidden',
            'N' => 'default Visible',
            'O' => 'default Visible',
            'P' => 'default Visible',
            'Q' => 'default Visible',
            'R' => 'default Visible',
            'S' => 'default Visible',
        ];
        $actual = [];
        for ($col = 'A'; $col < 'T'; StringHelper::stringIncrement($col)) {
            $width = $sheet->getColumnDimension($col)
                ->getWidth(Dimension::UOM_CENTIMETERS);
            $width = ($width == -1) ? 'default' : (round($width, 3) . 'cm');
            $visible = $sheet->getColumnDimension($col)->getVisible() ? 'Visible' : 'Hidden';
            $actual[$col] = "$width $visible";
        }
        self::assertSame($expected, $actual);
        $spreadsheet->disconnectWorksheets();
    }

    public static function testColspans2(): void
    {
        $infile = 'tests/data/Reader/Xml/colspans2.xml';
        $reader = new XmlReader();
        $spreadsheet = $reader->load($infile);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('XFD', $sheet->getHighestColumn(), 'column dimensions created all the way to end');
        self::assertSame('H', $sheet->getHighestDataColumn());
        $col = 'XFD';
        $width = $sheet->getColumnDimension($col)
            ->getWidth(Dimension::UOM_CENTIMETERS);
        $width = ($width == -1) ? 'default' : (round($width, 3) . 'cm');
        self::assertSame('default', $width);
        $visible = $sheet->getColumnDimension($col)->getVisible() ? 'Visible' : 'Hidden';
        self::assertSame('Hidden', $visible);
        $spreadsheet->disconnectWorksheets();
    }

    public static function testColspans3(): void
    {
        $infile = 'tests/data/Reader/Xml/colspans3.xml';
        $reader = new XmlReader();
        $spreadsheet = $reader->load($infile);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('H', $sheet->getHighestColumn(), 'no extra column dimensions created');
        self::assertSame('H', $sheet->getHighestDataColumn());
        $col = 'XFD';
        $width = $sheet->getColumnDimension($col)
            ->getWidth(Dimension::UOM_CENTIMETERS);
        $width = ($width == -1) ? 'default' : (round($width, 3) . 'cm');
        self::assertSame('default', $width);
        $visible = $sheet->getColumnDimension($col)->getVisible() ? 'Visible' : 'Hidden';
        self::assertSame('Visible', $visible);
        $spreadsheet->disconnectWorksheets();
    }
}

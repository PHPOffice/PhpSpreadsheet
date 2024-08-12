<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class NamespaceStdTest extends \PHPUnit\Framework\TestCase
{
    private static string $testbook = 'tests/data/Reader/XLSX/namespacestd.xlsx';

    public function testPreliminaries(): void
    {
        $file = 'zip://';
        $file .= self::$testbook;
        $file .= '#xl/workbook.xml';
        $data = file_get_contents($file);
        // confirm that file contains expected namespaced xml tag
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            if (!str_contains(__FILE__, 'NonStd')) {
                self::assertStringNotContainsString('nonstd', self::$testbook);
                self::assertStringContainsString('<workbook ', $data);
            } else {
                self::assertStringContainsString('nonstd', self::$testbook);
                self::assertStringContainsString('<x:workbook ', $data);
            }
        }
    }

    public function testInfo(): void
    {
        $reader = new Xlsx();
        $workSheetInfo = $reader->listWorkSheetInfo(self::$testbook);
        $info0 = $workSheetInfo[0];
        self::assertEquals('SylkTest', $info0['worksheetName']);
        self::assertEquals('J', $info0['lastColumnLetter']);
        self::assertEquals(9, $info0['lastColumnIndex']);
        self::assertEquals(18, $info0['totalRows']);
        self::assertEquals(10, $info0['totalColumns']);
    }

    public function testSheetNames(): void
    {
        $reader = new Xlsx();
        $worksheetNames = $reader->listWorksheetNames(self::$testbook);
        self::assertEquals(['SylkTest', 'Second'], $worksheetNames);
    }

    public function testActive(): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load(self::$testbook);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('Second', $sheet->getTitle());
        self::assertSame('A2', $sheet->getFreezePane());
        self::assertSame('A2', $sheet->getTopLeftCell());
        self::assertSame('B3', $sheet->getSelectedCells());
        $sheet = $spreadsheet->getSheetByNameOrThrow('SylkTest');
        self::assertNull($sheet->getFreezePane());
        self::assertNull($sheet->getTopLeftCell());
    }

    public function testLoadXlsx(): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load(self::$testbook);
        $sheet = $spreadsheet->getSheet(0);
        self::assertEquals('SylkTest', $sheet->getTitle());
        if (str_contains(__FILE__, 'NonStd')) {
            self::markTestIncomplete('Not yet ready');
        }

        self::assertEquals('FFFF0000', $sheet->getCell('A1')->getStyle()->getFont()->getColor()->getARGB());
        self::assertEquals(Fill::FILL_PATTERN_GRAY125, $sheet->getCell('A2')->getStyle()->getFill()->getFillType());
        self::assertEquals(Font::UNDERLINE_SINGLE, $sheet->getCell('A4')->getStyle()->getFont()->getUnderline());
        self::assertEquals('Test with (;) in string', $sheet->getCell('A4')->getValue());

        self::assertEquals(22269, $sheet->getCell('A10')->getValue());
        self::assertEquals('dd/mm/yyyy', $sheet->getCell('A10')->getStyle()->getNumberFormat()->getFormatCode());
        self::assertEquals('19/12/1960', $sheet->getCell('A10')->getFormattedValue());
        self::assertEquals(1.5, $sheet->getCell('A11')->getValue());
        self::assertEquals('# ?/?', $sheet->getCell('A11')->getStyle()->getNumberFormat()->getFormatCode());
        self::assertEquals('1 1/2', $sheet->getCell('A11')->getFormattedValue());

        self::assertEquals('=B1+C1', $sheet->getCell('H1')->getValue());
        self::assertEquals('=E2&F2', $sheet->getCell('J2')->getValue());
        self::assertEquals('=SUM(C1:C4)', $sheet->getCell('I5')->getValue());
        self::assertEquals('=MEDIAN(B6:B8)', $sheet->getCell('B9')->getValue());

        self::assertEquals(11, $sheet->getCell('E1')->getStyle()->getFont()->getSize());
        self::assertTrue($sheet->getCell('E1')->getStyle()->getFont()->getBold());
        self::assertTrue($sheet->getCell('E1')->getStyle()->getFont()->getItalic());
        self::assertEquals(Font::UNDERLINE_SINGLE, $sheet->getCell('E1')->getStyle()->getFont()->getUnderline());
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
        self::assertEquals(Font::UNDERLINE_SINGLE, $sheet->getCell('F1')->getStyle()->getFont()->getUnderline());
        self::assertFalse($sheet->getCell('F2')->getStyle()->getFont()->getBold());
        self::assertFalse($sheet->getCell('F2')->getStyle()->getFont()->getItalic());
        self::assertEquals(Font::UNDERLINE_NONE, $sheet->getCell('F2')->getStyle()->getFont()->getUnderline());
        self::assertTrue($sheet->getCell('F3')->getStyle()->getFont()->getBold());
        self::assertTrue($sheet->getCell('F3')->getStyle()->getFont()->getItalic());
        self::assertEquals(Font::UNDERLINE_NONE, $sheet->getCell('F3')->getStyle()->getFont()->getUnderline());
        self::assertFalse($sheet->getCell('F4')->getStyle()->getFont()->getBold());
        self::assertFalse($sheet->getCell('F4')->getStyle()->getFont()->getItalic());
        self::assertEquals(Font::UNDERLINE_NONE, $sheet->getCell('F4')->getStyle()->getFont()->getUnderline());

        self::assertEquals(Border::BORDER_THIN, $sheet->getCell('C10')->getStyle()->getBorders()->getTop()->getBorderStyle());
        self::assertEquals(Border::BORDER_NONE, $sheet->getCell('C10')->getStyle()->getBorders()->getRight()->getBorderStyle());
        self::assertEquals(Border::BORDER_NONE, $sheet->getCell('C10')->getStyle()->getBorders()->getBottom()->getBorderStyle());
        self::assertEquals(Border::BORDER_NONE, $sheet->getCell('C10')->getStyle()->getBorders()->getLeft()->getBorderStyle());
        self::assertEquals(Border::BORDER_NONE, $sheet->getCell('C12')->getStyle()->getBorders()->getTop()->getBorderStyle());
        self::assertEquals(Border::BORDER_NONE, $sheet->getCell('C12')->getStyle()->getBorders()->getRight()->getBorderStyle());
        self::assertEquals(Border::BORDER_THIN, $sheet->getCell('C12')->getStyle()->getBorders()->getBottom()->getBorderStyle());
        self::assertEquals(Border::BORDER_NONE, $sheet->getCell('C12')->getStyle()->getBorders()->getLeft()->getBorderStyle());
        self::assertEquals(Border::BORDER_NONE, $sheet->getCell('C14')->getStyle()->getBorders()->getTop()->getBorderStyle());
        self::assertEquals(Border::BORDER_NONE, $sheet->getCell('C14')->getStyle()->getBorders()->getRight()->getBorderStyle());
        self::assertEquals(Border::BORDER_NONE, $sheet->getCell('C14')->getStyle()->getBorders()->getBottom()->getBorderStyle());
        self::assertEquals(Border::BORDER_THIN, $sheet->getCell('C14')->getStyle()->getBorders()->getLeft()->getBorderStyle());
        self::assertEquals(Border::BORDER_NONE, $sheet->getCell('C16')->getStyle()->getBorders()->getTop()->getBorderStyle());
        self::assertEquals(Border::BORDER_THIN, $sheet->getCell('C16')->getStyle()->getBorders()->getRight()->getBorderStyle());
        self::assertEquals(Border::BORDER_NONE, $sheet->getCell('C16')->getStyle()->getBorders()->getBottom()->getBorderStyle());
        self::assertEquals(Border::BORDER_NONE, $sheet->getCell('C16')->getStyle()->getBorders()->getLeft()->getBorderStyle());
        self::assertEquals(Border::BORDER_THIN, $sheet->getCell('C18')->getStyle()->getBorders()->getTop()->getBorderStyle());
        self::assertEquals(Border::BORDER_THIN, $sheet->getCell('C18')->getStyle()->getBorders()->getRight()->getBorderStyle());
        self::assertEquals(Border::BORDER_THIN, $sheet->getCell('C18')->getStyle()->getBorders()->getBottom()->getBorderStyle());
        self::assertEquals(Border::BORDER_THIN, $sheet->getCell('C18')->getStyle()->getBorders()->getLeft()->getBorderStyle());
    }

    public function testLoadXlsxSheet2Contents(): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load(self::$testbook);
        $sheet = $spreadsheet->getSheet(1);
        self::assertEquals('Second', $sheet->getTitle());
        self::assertSame('Hyperlink', $sheet->getCell('B2')->getValue());
        $hyper = $sheet->getCell('B2')->getHyperlink();
        self::assertSame('http://www.example.com/', $hyper->getUrl());
        self::assertSame('Comment', $sheet->getCell('B3')->getValue());
        $comment = $sheet->getComment('B3');
        // Created as "threaded comment" with Excel 365, not quite as expected.
        self::assertStringContainsString('This is a comment', (string) $comment);
    }

    public function testLoadXlsxSheet2Styles(): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load(self::$testbook);
        $sheet = $spreadsheet->getSheet(1);
        self::assertEquals('Second', $sheet->getTitle());
        if (str_contains(__FILE__, 'NonStd')) {
            self::markTestIncomplete('Not yet ready');
        }
        self::assertEquals('center', $sheet->getCell('A2')->getStyle()->getAlignment()->getHorizontal());
        self::assertSame('inherit', $sheet->getCell('A2')->getStyle()->getProtection()->getLocked());
        self::assertEquals('top', $sheet->getCell('A3')->getStyle()->getAlignment()->getVertical());
        self::assertSame('unprotected', $sheet->getCell('A3')->getStyle()->getProtection()->getLocked());
    }
}

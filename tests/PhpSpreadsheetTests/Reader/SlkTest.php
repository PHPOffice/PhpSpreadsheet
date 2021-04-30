<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\Slk;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class SlkTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string
     */
    private static $testbook = __DIR__ . '/../../../samples/templates/SylkTest.slk';

    /**
     * @var string
     */
    private $filename = '';

    protected function teardown(): void
    {
        if ($this->filename) {
            unlink($this->filename);
            $this->filename = '';
        }
    }

    public function testInfo(): void
    {
        $reader = new Slk();
        $workSheetInfo = $reader->listWorkSheetInfo(self::$testbook);
        $info0 = $workSheetInfo[0];
        self::assertEquals('SylkTest', $info0['worksheetName']);
        self::assertEquals('J', $info0['lastColumnLetter']);
        self::assertEquals(9, $info0['lastColumnIndex']);
        self::assertEquals(18, $info0['totalRows']);
        self::assertEquals(10, $info0['totalColumns']);
    }

    public function testBadFileName(): void
    {
        $this->expectException(ReaderException::class);
        $reader = new Slk();
        self::assertNull($reader->setLoadSheetsOnly(null)->getLoadSheetsOnly());
        $reader->listWorkSheetInfo(self::$testbook . 'xxx');
    }

    public function testBadFileName2(): void
    {
        $reader = new Slk();
        self::assertFalse($reader->canRead(self::$testbook . 'xxx'));
    }

    public function testNotSylkFile(): void
    {
        $this->expectException(ReaderException::class);
        $reader = new Slk();
        $reader->listWorkSheetInfo(__FILE__);
    }

    public function testLoadSlk(): void
    {
        $reader = new Slk();
        $spreadsheet = $reader->load(self::$testbook);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertEquals('SylkTest', $sheet->getTitle());

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
        // Have not yet figured out how C6/C7 are centred
    }

    public function testSheetIndex(): void
    {
        $reader = new Slk();
        $sheetIndex = 2;
        $reader->setSheetIndex($sheetIndex);
        self::assertEquals($sheetIndex, $reader->getSheetIndex());
        $spreadsheet = $reader->load(self::$testbook);
        $sheet = $spreadsheet->setActiveSheetIndex($sheetIndex);
        self::assertEquals('SylkTest', $sheet->getTitle());

        self::assertEquals('FFFF0000', $sheet->getCell('A1')->getStyle()->getFont()->getColor()->getARGB());
    }

    public function testLongName(): void
    {
        $contents = file_get_contents(self::$testbook);
        $this->filename = File::sysGetTempDir()
            . '/123456789a123456789b123456789c12345.slk';
        file_put_contents($this->filename, $contents);
        $reader = new Slk();
        $spreadsheet = $reader->load($this->filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertEquals('123456789a123456789b123456789c1', $sheet->getTitle());
        self::assertEquals('FFFF0000', $sheet->getCell('A1')->getStyle()->getFont()->getColor()->getARGB());
    }
}

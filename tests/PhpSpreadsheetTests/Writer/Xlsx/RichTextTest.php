<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

class RichTextTest extends TestCase
{
    private string $filename = '';

    protected function teardown(): void
    {
        if ($this->filename !== '') {
            unlink($this->filename);
        }
    }

    public function testRichText(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $rtf = new RichText();
        $rtf->createText('~Cell Style~');
        $rtf->createTextRun('~RTF Style~')->getFont()?->setItalic(true);
        $rtf->createText('~No Style~');
        $sheet->getCell('A1')->setValue($rtf);
        $sheet->getStyle('A1')->getFont()->setBold(true);

        $fontStyle = $sheet->getStyle('A1')->getFont();
        self::assertTrue($fontStyle->getBold());
        self::assertFalse($fontStyle->getItalic());

        $a1Value = $sheet->getCell('A1')->getValue();
        self::assertInstanceOf(RichText::class, $a1Value);
        $elements = $a1Value->getRichTextElements();
        self::assertCount(3, $elements);
        self::assertNull($elements[0]->getFont());
        $fontStyle = $elements[1]->getFont();
        self::assertNotNull($fontStyle);
        self::assertFalse($fontStyle->getBold());
        self::assertTrue($fontStyle->getItalic());
        self::assertNull($elements[0]->getFont());

        $this->filename = File::temporaryFilename();
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($this->filename);
        $spreadsheet->disconnectWorksheets();

        $this->readfile();
    }

    private function readfile(): void
    {
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($this->filename);
        $sheet = $spreadsheet->getActiveSheet();
        $fontStyle = $sheet->getStyle('A1')->getFont();
        self::assertTrue($fontStyle->getBold());
        self::assertFalse($fontStyle->getItalic());

        $a1Value = $sheet->getCell('A1')->getValue();
        self::assertInstanceOf(RichText::class, $a1Value);
        $elements = $a1Value->getRichTextElements();
        self::assertCount(3, $elements);
        // write/read has changed text to run but no real difference
        $fontStyle = $elements[0]->getFont();
        self::assertNotNull($fontStyle);
        self::assertTrue($fontStyle->getBold());
        self::assertFalse($fontStyle->getItalic());
        $fontStyle = $elements[1]->getFont();
        self::assertNotNull($fontStyle);
        self::assertFalse($fontStyle->getBold());
        self::assertTrue($fontStyle->getItalic());
        // write/read has changed text to run but no real difference
        $fontStyle = $elements[2]->getFont();
        self::assertNotNull($fontStyle);
        self::assertTrue($fontStyle->getBold());
        self::assertFalse($fontStyle->getItalic());

        $spreadsheet->disconnectWorksheets();
    }
}

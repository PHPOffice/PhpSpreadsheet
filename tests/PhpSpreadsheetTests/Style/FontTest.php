<?php

namespace PhpOffice\PhpSpreadsheetTests\Style;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PHPUnit\Framework\TestCase;

class FontTest extends TestCase
{
    public function testSuperSubScript(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cell = $sheet->getCell('A1');
        $cell->setValue('Cell A1');
        $font = $cell->getStyle()->getFont();
        $font->setSuperscript(true);
        $font->setSubscript(true);
        self::assertFalse($font->getSuperscript(), 'Earlier set true loses');
        self::assertTrue($font->getSubscript(), 'Last set true wins');
        $font->setSubscript(true);
        $font->setSuperscript(true);
        self::assertTrue($font->getSuperscript(), 'Last set true wins');
        self::assertFalse($font->getSubscript(), 'Earlier set true loses');
        $font->setSuperscript(false);
        $font->setSubscript(false);
        self::assertFalse($font->getSuperscript(), 'False remains unchanged');
        self::assertFalse($font->getSubscript(), 'False remains unchanged');
        $font->setSubscript(false);
        $font->setSuperscript(false);
        self::assertFalse($font->getSuperscript(), 'False remains unchanged');
        self::assertFalse($font->getSubscript(), 'False remains unchanged');
        $font->setSubscript(true);
        $font->setSuperscript(false);
        self::assertFalse($font->getSuperscript(), 'False remains unchanged');
        self::assertTrue($font->getSubscript(), 'True remains unchanged');
        $font->setSubscript(false);
        $font->setSuperscript(true);
        self::assertTrue($font->getSuperscript());
        self::assertFalse($font->getSubscript(), 'False remains unchanged');
        $spreadsheet->disconnectWorksheets();
    }

    public function testSize(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cell = $sheet->getCell('A1');
        $cell->setValue('Cell A1');
        $font = $cell->getStyle()->getFont();

        self::assertEquals(11, $font->getSize(), 'The default is 11');

        $font->setSize(12);
        self::assertEquals(12, $font->getSize(), 'Accepted new font size');

        $invalidFontSizeValues = [
            '',
            false,
            true,
            'non_numeric_string',
            '-1.0',
            -1.0,
            0,
            [],
            (object) [],
            null,
        ];
        foreach ($invalidFontSizeValues as $invalidFontSizeValue) {
            $font->setSize(12);
            $font->setSize($invalidFontSizeValue);
            self::assertEquals(10, $font->getSize(), 'Set to 10 after trying to set an invalid value.');
        }
        $spreadsheet->disconnectWorksheets();
    }

    public function testName(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cell = $sheet->getCell('A1');
        $cell->setValue('Cell A1');
        $font = $cell->getStyle()->getFont();
        self::assertEquals('Calibri', $font->getName(), 'The default is Calibri');
        $font->setName('whatever');
        self::assertEquals('whatever', $font->getName(), 'The default is Calibri');
        $font->setName('');
        self::assertEquals('Calibri', $font->getName(), 'Null string changed to default');
        $spreadsheet->disconnectWorksheets();
    }

    public function testUnderlineHash(): void
    {
        $font1 = new Font();
        $font2 = new Font();
        $font2aHash = $font2->getHashCode();
        self::assertSame($font1->getHashCode(), $font2aHash);
        $font2->setUnderlineColor(
            [
                'type' => 'srgbClr',
                'value' => 'FF0000',
            ]
        );
        $font2bHash = $font2->getHashCode();
        self::assertNotEquals($font1->getHashCode(), $font2bHash);
    }
}

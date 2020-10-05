<?php

namespace PhpOffice\PhpSpreadsheetTests\Style;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
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
    }
}

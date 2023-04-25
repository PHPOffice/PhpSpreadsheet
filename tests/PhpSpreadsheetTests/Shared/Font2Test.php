<?php

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Shared\Font;
use PhpOffice\PhpSpreadsheet\Style\Font as StyleFont;
use PHPUnit\Framework\TestCase;

class Font2Test extends TestCase
{
    /**
     * @dataProvider providerCharsetFromFontName
     */
    public function testCharsetFromFontName(string $fontName, int $expectedResult): void
    {
        $result = Font::getCharsetFromFontName($fontName);
        self::assertEquals($expectedResult, $result);
    }

    public function testCharsetFromFontNameCoverage(): void
    {
        $covered = [];
        $expected = Font::CHARSET_FROM_FONT_NAME;
        foreach (array_keys($expected) as $key) {
            $covered[$key] = 0;
        }
        $defaultCovered = false;
        $tests = $this->providerCharsetFromFontName();
        foreach ($tests as $test) {
            $thisTest = $test[0];
            if (array_key_exists($thisTest, $covered)) {
                $covered[$thisTest] = 1;
            } else {
                $defaultCovered = true;
            }
        }
        foreach ($covered as $key => $val) {
            self::assertEquals(1, $val, "FontName $key not tested");
        }
        self::assertTrue($defaultCovered, 'Default key not tested');
    }

    public static function providerCharsetFromFontName(): array
    {
        return [
            ['EucrosiaUPC', Font::CHARSET_ANSI_THAI],
            ['Wingdings', Font::CHARSET_SYMBOL],
            ['Wingdings 2', Font::CHARSET_SYMBOL],
            ['Wingdings 3', Font::CHARSET_SYMBOL],
            ['Default', Font::CHARSET_ANSI_LATIN],
        ];
    }

    public function testColumnWidths(): void
    {
        $widths = Font::DEFAULT_COLUMN_WIDTHS;
        $fontNames = ['Arial', 'Calibri', 'Verdana'];
        $font = new StyleFont();
        foreach ($fontNames as $fontName) {
            $font->setName($fontName);
            $array = $widths[$fontName];
            foreach ($array as $points => $array2) {
                $font->setSize($points);
                $px = $array2['px'];
                $width = $array2['width'];
                self::assertEquals($px, Font::getDefaultColumnWidthByFont($font, true), "$fontName $points px");
                self::assertEquals($width, Font::getDefaultColumnWidthByFont($font, false), "$fontName $points ooxml-units");
            }
        }
        $pxCalibri11 = $widths['Calibri'][11]['px'];
        $widthCalibri11 = $widths['Calibri'][11]['width'];
        $fontName = 'unknown';
        $points = 11;
        $font->setName($fontName);
        $font->setSize($points);
        self::assertEquals($pxCalibri11, Font::getDefaultColumnWidthByFont($font, true), "$fontName $points px");
        self::assertEquals($widthCalibri11, Font::getDefaultColumnWidthByFont($font, false), "$fontName $points ooxml-units");
        $points = 22;
        $font->setSize($points);
        self::assertEquals(2 * $pxCalibri11, Font::getDefaultColumnWidthByFont($font, true), "$fontName $points px");
        self::assertEquals(2 * $widthCalibri11, Font::getDefaultColumnWidthByFont($font, false), "$fontName $points ooxml-units");
        $fontName = 'Arial';
        $points = 33;
        $font->setName($fontName);
        $font->setSize($points);
        self::assertEquals(3 * $pxCalibri11, Font::getDefaultColumnWidthByFont($font, true), "$fontName $points px");
        self::assertEquals(3 * $widthCalibri11, Font::getDefaultColumnWidthByFont($font, false), "$fontName $points ooxml-units");
    }

    public function testRowHeights(): void
    {
        $heights = Font::DEFAULT_COLUMN_WIDTHS;
        $fontNames = ['Arial', 'Calibri', 'Verdana'];
        $font = new StyleFont();
        foreach ($fontNames as $fontName) {
            $font->setName($fontName);
            $array = $heights[$fontName];
            foreach ($array as $points => $array2) {
                $font->setSize($points);
                $height = $array2['height'];
                self::assertEquals($height, Font::getDefaultRowHeightByFont($font), "$fontName $points points");
            }
        }
        $heightArial10 = $heights['Arial'][10]['height'];
        $fontName = 'Arial';
        $points = 20;
        $font->setName($fontName);
        $font->setSize($points);
        self::assertEquals(2 * $heightArial10, Font::getDefaultRowHeightByFont($font), "$fontName $points points");
        $heightVerdana10 = $heights['Verdana'][10]['height'];
        $fontName = 'Verdana';
        $points = 30;
        $font->setName($fontName);
        $font->setSize($points);
        self::assertEquals(3 * $heightVerdana10, Font::getDefaultRowHeightByFont($font), "$fontName $points points");
        $heightCalibri11 = $heights['Calibri'][11]['height'];
        $fontName = 'Calibri';
        $points = 22;
        $font->setName($fontName);
        $font->setSize($points);
        self::assertEquals(2 * $heightCalibri11, Font::getDefaultRowHeightByFont($font), "$fontName $points points");
        $fontName = 'unknown';
        $points = 33;
        $font->setName($fontName);
        $font->setSize($points);
        self::assertEquals(3 * $heightCalibri11, Font::getDefaultRowHeightByFont($font), "$fontName $points points");
    }

    public function testGetTrueTypeFontFileFromFont(): void
    {
        $fileNames = Font::FONT_FILE_NAMES;
        $font = new StyleFont();
        foreach ($fileNames as $fontName => $fontNameArray) {
            $font->setName($fontName);
            $font->setBold(false);
            $font->setItalic(false);
            self::assertSame($fileNames[$fontName]['x'], Font::getTrueTypeFontFileFromFont($font, false), "$fontName not bold not italic");
            $font->setBold(true);
            $font->setItalic(false);
            self::assertSame($fileNames[$fontName]['xb'], Font::getTrueTypeFontFileFromFont($font, false), "$fontName bold not italic");
            $font->setBold(false);
            $font->setItalic(true);
            self::assertSame($fileNames[$fontName]['xi'], Font::getTrueTypeFontFileFromFont($font, false), "$fontName not bold italic");
            $font->setBold(true);
            $font->setItalic(true);
            self::assertSame($fileNames[$fontName]['xbi'], Font::getTrueTypeFontFileFromFont($font, false), "$fontName bold italic");
        }
    }
}

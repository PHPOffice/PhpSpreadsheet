<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Theme as SpreadsheetTheme;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ThemeColorsTest extends AbstractFunctional
{
    private const COLOR_SCHEME_2013_PLUS_NAME = 'Office 2013+';

    public function testOffice2013Theme(): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getTheme()
            ->setThemeColorName(
                self::COLOR_SCHEME_2013_PLUS_NAME
            );
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        self::assertSame(
            SpreadsheetTheme::COLOR_SCHEME_2013_2022_NAME,
            $reloadedSpreadsheet->getTheme()->getThemeColorName()
        );
        self::assertSame('FFC000', $reloadedSpreadsheet->getTheme()->getThemeColors()['accent4']);
        self::assertSame('Calibri Light', $reloadedSpreadsheet->getTheme()->getMajorFontLatin());
        self::assertSame('Calibri', $reloadedSpreadsheet->getTheme()->getMinorFontLatin());
        $defaultFont2 = $reloadedSpreadsheet->getDefaultStyle()->getFont()->getName();
        self::assertSame('Calibri', $defaultFont2);
        $font3 = $reloadedSpreadsheet->getActiveSheet()
            ->getStyle('Z10')->getFont()->getName();
        self::assertSame('Calibri', $font3);
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testOffice2013Theme2(): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getTheme()
            ->setThemeColorName(
                SpreadsheetTheme::COLOR_SCHEME_2013_2022_NAME
            );
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        self::assertSame(
            SpreadsheetTheme::COLOR_SCHEME_2013_2022_NAME,
            $reloadedSpreadsheet->getTheme()->getThemeColorName()
        );
        self::assertSame('FFC000', $reloadedSpreadsheet->getTheme()->getThemeColors()['accent4']);
        self::assertSame('Calibri Light', $reloadedSpreadsheet->getTheme()->getMajorFontLatin());
        self::assertSame('Calibri', $reloadedSpreadsheet->getTheme()->getMinorFontLatin());
        $defaultFont2 = $reloadedSpreadsheet->getDefaultStyle()->getFont()->getName();
        self::assertSame('Calibri', $defaultFont2);
        $font3 = $reloadedSpreadsheet->getActiveSheet()
            ->getStyle('Z10')->getFont()->getName();
        self::assertSame('Calibri', $font3);
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testOffice2007Theme(): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getTheme()->setThemeColorName(SpreadsheetTheme::COLOR_SCHEME_2007_2010_NAME);
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        self::assertSame('Office 2007-2010', $reloadedSpreadsheet->getTheme()->getThemeColorName());
        self::assertSame('8064A2', $reloadedSpreadsheet->getTheme()->getThemeColors()['accent4']);
        self::assertSame('Cambria', $reloadedSpreadsheet->getTheme()->getMajorFontLatin());
        self::assertSame('Calibri', $reloadedSpreadsheet->getTheme()->getMinorFontLatin());
        $defaultFont2 = $reloadedSpreadsheet->getDefaultStyle()->getFont()->getName();
        self::assertSame('Calibri', $defaultFont2);
        $font3 = $reloadedSpreadsheet->getActiveSheet()
            ->getStyle('Z10')->getFont()->getName();
        self::assertSame('Calibri', $font3);
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testDefaultTheme(): void
    {
        $spreadsheet = new Spreadsheet();
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        self::assertSame('Office', $reloadedSpreadsheet->getTheme()->getThemeColorName());
        self::assertSame('8064A2', $reloadedSpreadsheet->getTheme()->getThemeColors()['accent4']);
        self::assertSame('Cambria', $reloadedSpreadsheet->getTheme()->getMajorFontLatin());
        self::assertSame('Calibri', $reloadedSpreadsheet->getTheme()->getMinorFontLatin());
        $defaultFont2 = $reloadedSpreadsheet->getDefaultStyle()->getFont()->getName();
        self::assertSame('Calibri', $defaultFont2);
        $font3 = $reloadedSpreadsheet->getActiveSheet()
            ->getStyle('Z10')->getFont()->getName();
        self::assertSame('Calibri', $font3);
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testGalleryTheme(): void
    {
        $reader = new XlsxReader();
        $spreadsheet = $reader->load('tests/data/Writer/XLSX/gallerytheme.xlsx');
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        self::assertSame('Gallery', $reloadedSpreadsheet->getTheme()->getThemeColorName());
        self::assertSame('795FAF', $reloadedSpreadsheet->getTheme()->getThemeColors()['accent4']);
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testOffice2023Theme(): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getTheme()
            ->setThemeColorName(
                SpreadsheetTheme::COLOR_SCHEME_2023_PLUS_NAME,
                null,
                $spreadsheet
            );
        self::assertSame('Aptos Narrow', $spreadsheet->getDefaultStyle()->getFont()->getName(), 'default style is attached to spreadsheet');
        $style = new Style();
        self::assertSame('Calibri', $style->getFont()->getName(), 'style not attached to spreadsheet');
        $style2 = $spreadsheet->getActiveSheet()->getStyle('A7');
        self::assertSame('Aptos Narrow', $style2->getFont()->getName(), 'font is attached to spreadsheet');
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        self::assertSame(
            SpreadsheetTheme::COLOR_SCHEME_2023_PLUS_NAME,
            $reloadedSpreadsheet->getTheme()->getThemeColorName()
        );
        self::assertSame('0F9ED5', $reloadedSpreadsheet->getTheme()->getThemeColors()['accent4']);
        self::assertSame('Aptos Display', $reloadedSpreadsheet->getTheme()->getMajorFontLatin());
        self::assertSame('Aptos Narrow', $reloadedSpreadsheet->getTheme()->getMinorFontLatin());
        $defaultFont = $reloadedSpreadsheet->getDefaultStyle()->getFont()->getName();
        self::assertSame('Aptos Narrow', $defaultFont);
        $defaultFont2 = $reloadedSpreadsheet->getDefaultStyle()->getFont()->getName();
        self::assertSame('Aptos Narrow', $defaultFont2);
        $font3 = $reloadedSpreadsheet->getActiveSheet()
            ->getStyle('Z10')->getFont()->getName();
        self::assertSame('Aptos Narrow', $font3);
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}

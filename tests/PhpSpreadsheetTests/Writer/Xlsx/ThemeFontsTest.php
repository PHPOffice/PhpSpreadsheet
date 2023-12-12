<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ThemeFontsTest extends AbstractFunctional
{
    public function testFont2(): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getTheme()
            ->setThemeFontName('custom')
            ->setMajorFontValues('Arial', 'Arial', 'Arial', [])
            ->setMinorFontValues('Arial', 'Arial', 'Arial', []);
        $spreadsheet->getDefaultStyle()
            ->getFont()
            ->setName('Arial')
            ->setScheme('minor');
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue('header');
        $sheet->getStyle('A1')->getFont()->setScheme('major');
        $sheet->getCell('A2')->setValue('body');
        $sheet->getStyle('A2')->getFont()->setScheme('minor');
        $sheet->getCell('A3')->setValue('default');
        $sheet->getCell('A4')->setValue('noscheme');
        $sheet->getStyle('A4')->getFont()->setScheme('');
        $sheet->getCell('A5')->setValue('nottheme');
        $sheet->getStyle('A5')->getFont()->setName('Courier New');
        $sheet->getCell('A6')->setValue('dflt bold');
        $sheet->getStyle('A6')->getFont()->setBold(true);
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        self::assertSame('Arial', $spreadsheet->getTheme()->getMajorFontLatin());
        self::assertSame('Arial', $spreadsheet->getTheme()->getMinorFontLatin());
        $rsheet = $reloadedSpreadsheet->getActiveSheet();
        self::assertSame('Arial', $rsheet->getStyle('A1')->getFont()->getName());
        self::assertSame('major', $rsheet->getStyle('A1')->getFont()->getScheme());
        self::assertSame('Arial', $rsheet->getStyle('A2')->getFont()->getName());
        self::assertSame('minor', $rsheet->getStyle('A2')->getFont()->getScheme());
        self::assertSame('Arial', $rsheet->getStyle('A3')->getFont()->getName());
        self::assertSame('minor', $rsheet->getStyle('A3')->getFont()->getScheme());
        self::assertSame('Arial', $rsheet->getStyle('A4')->getFont()->getName());
        self::assertSame('', $rsheet->getStyle('A4')->getFont()->getScheme());
        self::assertSame('Courier New', $rsheet->getStyle('A5')->getFont()->getName());
        self::assertSame('', $rsheet->getStyle('A5')->getFont()->getScheme(), 'setting name disables scheme');
        self::assertSame('Arial', $rsheet->getStyle('A6')->getFont()->getName());
        self::assertSame('minor', $rsheet->getStyle('A6')->getFont()->getScheme());
        self::assertTrue($rsheet->getStyle('A6')->getFont()->getBold(), 'setting other properties does not disable scheme');

        $reloadedSpreadsheet->getTheme()
            ->setThemeFontName('custom')
            ->setMajorFontValues('Times New Roman', 'Times New Roman', 'Times New Roman', [])
            ->setMinorFontValues('Tahoma', 'Tahoma', 'Tahoma', []);
        $reloadedSpreadsheet->resetThemeFonts();
        self::assertSame('Times New Roman', $rsheet->getStyle('A1')->getFont()->getName());
        self::assertSame('major', $rsheet->getStyle('A1')->getFont()->getScheme());
        self::assertSame('Tahoma', $rsheet->getStyle('A2')->getFont()->getName());
        self::assertSame('minor', $rsheet->getStyle('A2')->getFont()->getScheme());
        self::assertSame('Tahoma', $rsheet->getStyle('A3')->getFont()->getName());
        self::assertSame('minor', $rsheet->getStyle('A3')->getFont()->getScheme());
        self::assertSame('Arial', $rsheet->getStyle('A4')->getFont()->getName());
        self::assertSame('', $rsheet->getStyle('A4')->getFont()->getScheme());
        self::assertSame('Courier New', $rsheet->getStyle('A5')->getFont()->getName());
        self::assertSame('', $rsheet->getStyle('A5')->getFont()->getScheme(), 'setting name disables scheme');
        self::assertSame('Tahoma', $rsheet->getStyle('A6')->getFont()->getName());
        self::assertSame('minor', $rsheet->getStyle('A6')->getFont()->getScheme());
        self::assertTrue($rsheet->getStyle('A6')->getFont()->getBold(), 'setting other properties does not disable scheme');

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}

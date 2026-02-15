<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Reader\Ods as OdsReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class FontStyleTest extends AbstractFunctional
{
    public function testReadFontStyles(): void
    {
        $spreadsheetOld = new Spreadsheet();
        $sheet = $spreadsheetOld->getActiveSheet();
        $spreadsheetOld->getDefaultStyle()
            ->getFont()
            ->setName('Liberation Sans')
            ->setSize(10);
        $sheet->getCell('A1')->setValue(13);
        $sheet->getCell('A2')->setValue(27);
        $sheet->getStyle('A2')->getFont()
            ->setBold(true)
            ->setItalic(true);
        $sheet->getCell('A3')->setValue(-14);
        $sheet->getStyle('A3')->getFont()
            ->setBold(true);
        $sheet->getCell('A4')->setValue(0);
        $sheet->getStyle('A4')->getFont()
            ->setItalic(true);
        $sheet->getCell('A5')->setValue(100);
        $sheet->getStyle('A5')->getFont()
            ->setBold(true)
            ->getColor()->setRgb('FF0000');
        $sheet->getCell('A6')->setValue(200);
        $sheet->getStyle('A6')->getFont()
            ->setUnderline('single');
        $sheet->getCell('A7')->setValue(300);
        $sheet->getStyle('A7')->getFont()
            ->setStrikethrough(true);
        $sheet->getCell('A8')->setValue(400);
        $sheet->getStyle('A8')->getFont()
            ->setAutoColor(true);
        $sheet->getCell('A9')->setValue(500);
        $sheet->getStyle('A9')->getFont()
            ->setItalic(true);
        $sheet->getStyle('A9')->getFill()
            ->setFillType('solid')
            ->getStartColor()->setRgb('FF00FF');
        $spreadsheet = $this->writeAndReload($spreadsheetOld, 'Ods');
        $spreadsheetOld->disconnectWorksheets();

        $newSheet = $spreadsheet->getActiveSheet();
        self::assertSame('Liberation Sans', $spreadsheet->getDefaultStyle()->getFont()->getName());
        self::assertSame(10.0, $spreadsheet->getDefaultStyle()->getFont()->getSize());
        self::assertSame('Liberation Sans', $newSheet->getStyle('A1')->getFont()->getName());
        self::assertFalse(
            $newSheet->getStyle('A1')->getFont()->getBold()
        );
        self::assertFalse(
            $newSheet->getStyle('A1')->getFont()->getItalic()
        );
        self::assertTrue(
            $newSheet->getStyle('A2')->getFont()->getBold()
        );
        self::assertTrue(
            $newSheet->getStyle('A2')->getFont()->getItalic()
        );
        self::assertTrue(
            $newSheet->getStyle('A3')->getFont()->getBold()
        );
        self::assertFalse(
            $newSheet->getStyle('A3')->getFont()->getItalic()
        );
        self::assertFalse(
            $newSheet->getStyle('A4')->getFont()->getBold()
        );
        self::assertTrue(
            $newSheet->getStyle('A4')->getFont()->getItalic()
        );
        self::assertTrue(
            $newSheet->getStyle('A5')->getFont()->getBold()
        );
        self::assertFalse(
            $newSheet->getStyle('A5')->getFont()->getItalic()
        );
        self::assertSame(
            'FF0000',
            $newSheet->getStyle('A5')->getFont()
                ->getColor()
                ->getRgb()
        );
        self::assertSame('single', $newSheet->getStyle('A6')->getFont()->getUnderline());
        self::assertTrue(
            $newSheet->getStyle('A7')->getFont()->getStrikethrough()
        );
        self::assertTrue(
            $newSheet->getStyle('A8')->getFont()->getAutoColor()
        );
        self::assertSame(
            'solid',
            $newSheet->getStyle('A9')->getFill()->getFillType()
        );
        self::assertSame(
            'ff00ff',
            $newSheet->getStyle('A9')->getFill()
                ->getStartColor()
                ->getRgb()
        );
        $spreadsheet->disconnectWorksheets();
    }

    public static function testNoExplicitDefaultStyle(): void
    {
        $reader = new OdsReader();
        $infile = 'tests/data/Reader/Ods/odsstyles5.ods';
        $spreadsheet = $reader->load($infile);
        self::assertSame('Times New Roman', $spreadsheet->getDefaultStyle()->getFont()->getName());
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('Times New Roman', $sheet->getStyle('A1')->getFont()->getName());
        $spreadsheet->disconnectWorksheets();
    }
}

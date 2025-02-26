<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class RichTextTest extends AbstractFunctional
{
    public function testRichTextColors(): void
    {
        $spreadsheetOld = new Spreadsheet();
        $sheet = $spreadsheetOld->getActiveSheet();
        $richText = new RichText();
        $part1 = $richText->createTextRun('Red');
        $font1 = $part1->getFont();
        if ($font1 !== null) {
            $font1->setName('Courier New');
            $font1->getColor()->setArgb('FFFF0000');
        }
        $part2 = $richText->createTextRun('Blue');
        $font2 = $part2->getFont();
        if ($font2 !== null) {
            $font2->setName('Times New Roman');
            $font2->setItalic(true);
            $font2->getColor()->setArgb('FF0000FF');
        }
        $sheet->setCellValue('A1', $richText);

        $spreadsheet = $this->writeAndReload($spreadsheetOld, 'Xlsx');
        $spreadsheetOld->disconnectWorksheets();
        $rsheet = $spreadsheet->getActiveSheet();
        $value = $rsheet->getCell('A1')->getValue();
        if ($value instanceof RichText) {
            $elements = $value->getRichTextElements();
            self::assertCount(2, $elements);
            $font1a = $elements[0]->getFont();
            $font2a = $elements[1]->getFont();
            self::assertNotNull($font1a);
            self::assertNotNull($font2a);
            self::assertSame('Courier New', $font1a->getName());
            self::assertSame('FFFF0000', $font1a->getColor()->getArgb());
            self::assertFalse($font1a->getItalic());
            self::assertSame('Times New Roman', $font2a->getName());
            self::assertSame('FF0000FF', $font2a->getColor()->getArgb());
            self::assertTrue($font2a->getItalic());
        } else {
            self::fail('Did not see expected RichText');
        }
        $spreadsheet->disconnectWorksheets();
    }
}

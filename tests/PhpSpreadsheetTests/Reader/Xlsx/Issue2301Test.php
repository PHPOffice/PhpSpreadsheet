<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class Issue2301Test extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string
     */
    private static $testbook = 'tests/data/Reader/XLSX/issue.2301.xlsx';

    public static function testReadRichText(): void
    {
        $spreadsheet = IOFactory::load(self::$testbook);
        $sheet = $spreadsheet->getActiveSheet();
        $value = $sheet->getCell('B45')->getValue();
        self::assertInstanceOf(RichText::class, $value);
        $richtext = $value->getRichTextElements();
        $font = $richtext[1]->getFont();
        self::assertNotNull($font);
        self::assertSame('Arial CE', $font->getName());
        self::assertSame(9.0, $font->getSize());
        self::assertSame('protected', $sheet->getCell('BT10')->getStyle()->getProtection()->getHidden());
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
    }
}

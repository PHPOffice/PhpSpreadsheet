<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class RichTextSizeTest extends AbstractFunctional
{
    public function testRichTextRunSize(): void
    {
        $filename = 'tests/data/Reader/XLS/RichTextFontSize.xls';
        $reader = new Xls();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getSheetByNameOrThrow('橱柜门板');
        $text = $sheet->getCell('L15')->getValue();
        self::assertInstanceOf(RichText::class, $text);
        $elements = $text->getRichTextElements();
        self::assertEquals(10, $elements[2]->getFont()?->getSize());
        $spreadsheet->disconnectWorksheets();
    }
}

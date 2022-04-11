<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class RichTextSizeTest extends AbstractFunctional
{
    public function testRichTextRunSize(): void
    {
        $filename = 'tests/data/Reader/XLS/RichTextFontSize.xls';
        $reader = new Xls();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getSheetByName('橱柜门板');
        self::assertNotNull($sheet);
        $text = $sheet->getCell('L15')->getValue();
        $elements = $text->getRichTextElements();
        self::assertEquals(10, $elements[2]->getFont()->getSize());
        $spreadsheet->disconnectWorksheets();
    }
}

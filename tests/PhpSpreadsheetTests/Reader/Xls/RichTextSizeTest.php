<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class RichTextSizeTest extends AbstractFunctional
{
    /**
     * @var Spreadsheet
     */
    private $spreadsheet;

    protected function setup(): void
    {
        $filename = 'tests/data/Reader/XLS/RichTextFontSize.xls';
        $reader = new Xls();
        $this->spreadsheet = $reader->load($filename);
    }

    public function testRichTextRunSize(): void
    {
        $sheet = $this->spreadsheet->getSheetByName('橱柜门板');
        $text = $sheet->getCell('L15')->getValue();
        $elements = $text->getRichTextElements();
        self::assertEquals(10, $elements[2]->getFont()->getSize());
    }
}

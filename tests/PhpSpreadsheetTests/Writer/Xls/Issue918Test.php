<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xls;

use PhpOffice\PhpSpreadsheet\Helper\Html as HtmlHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Issue918Test extends AbstractFunctional
{
    public function testEmptyRichText(): void
    {
        // Issue 918 - Xls Writer creates corrupt file with empty RichText.
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $helper = new HtmlHelper();
        $html = '<div></div>';
        $richValue = $helper->toRichTextObject($html);
        self::assertCount(0, $richValue->getRichTextElements());
        $sheet->getCell('A1')->setValue($richValue);

        $robj = $this->writeAndReload($spreadsheet, 'Xls');
        $spreadsheet->disconnectWorksheets();
        $sheet0 = $robj->getActiveSheet();
        self::assertNull($sheet0->getCell('A1')->getValue(), 'empty text object has been changed to null');
        $robj->disconnectWorksheets();
    }
}

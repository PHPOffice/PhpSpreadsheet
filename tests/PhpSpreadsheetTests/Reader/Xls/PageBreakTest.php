<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class PageBreakTest extends AbstractFunctional
{
    public function testPageBreak(): void
    {
        $filename = 'samples/templates/50_xlsverticalbreak.xls';
        $reader = new Xls();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        $pageSetup = $sheet->getPageSetup();
        self::assertSame(PageSetup::PAGEORDER_DOWN_THEN_OVER, $pageSetup->getPageOrder());
        $breaks = $sheet->getBreaks();
        self::assertCount(2, $breaks);
        self::assertSame(Worksheet::BREAK_ROW, $breaks['A5']);
        self::assertSame(Worksheet::BREAK_COLUMN, $breaks['H1']);
        $newSpreadsheet = $this->writeAndReload($spreadsheet, 'Xls');
        $spreadsheet->disconnectWorksheets();

        $newSheet = $newSpreadsheet->getActiveSheet();
        $newPageSetup = $newSheet->getPageSetup();
        self::assertSame(PageSetup::PAGEORDER_DOWN_THEN_OVER, $newPageSetup->getPageOrder());
        $newBreaks = $newSheet->getBreaks();
        self::assertCount(2, $newBreaks);
        self::assertSame(Worksheet::BREAK_ROW, $newBreaks['A5']);
        self::assertSame(Worksheet::BREAK_COLUMN, $newBreaks['H1']);
        $newSpreadsheet->disconnectWorksheets();
    }
}

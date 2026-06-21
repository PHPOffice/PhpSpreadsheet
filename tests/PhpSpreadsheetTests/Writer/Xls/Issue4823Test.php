<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Html as HtmlReader;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Issue4823Test extends AbstractFunctional
{
    /**
     * Xlsx Writer did not handle image data uri's correctly (Xls was okay).
     */
    public function testIssue4823(): void
    {
        $infile = 'tests/data/Reader/HTML/issue.4823.html';
        $reader = new HtmlReader();
        $spreadsheet = $reader->load($infile);
        $sheet = $spreadsheet->getActiveSheet();
        $drawings = $sheet->getDrawingCollection();
        self::assertCount(1, $drawings);
        $drawing = $drawings[0];
        self::assertInstanceOf(Drawing::class, $drawing);
        self::assertStringStartsWith('data:image/png;base64,', $drawing->getPath());
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xls');
        $spreadsheet->disconnectWorksheets();
        $rsheet = $reloadedSpreadsheet->getActiveSheet();
        $drawings = $rsheet->getDrawingCollection();
        self::assertCount(1, $drawings);
        $drawing = $drawings[0];
        self::assertInstanceOf(MemoryDrawing::class, $drawing);
        self::assertSame('image/png', $drawing->getMimeType());
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}

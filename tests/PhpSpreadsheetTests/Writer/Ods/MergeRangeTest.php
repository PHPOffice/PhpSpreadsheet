<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Ods;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class MergeRangeTest extends AbstractFunctional
{
    public function testMergeRangeWriter(): void
    {
        $mergeRange = 'B2:C3';
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->setCellValue('B2', "Merge Range {$mergeRange}");
        $worksheet->mergeCells($mergeRange);

        $reloaded = $this->writeAndReload($spreadsheet, 'Ods');

        $cell = $reloaded->getActiveSheet()->getCell('B2');
        self::assertTrue($cell->isInMergeRange());
        self::assertSame($mergeRange, $cell->getMergeRange());
    }
}

<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Ods;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class AutoFilterTest extends AbstractFunctional
{
    public function testAutoFilterWriter(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        $dataSet = [
            ['Year', 'Quarter', 'Sales'],
            [2020, 'Q1', 100],
            [2020, 'Q2', 120],
            [2020, 'Q3', 140],
            [2020, 'Q4', 160],
            [2021, 'Q1', 180],
            [2021, 'Q2', 75],
            [2021, 'Q3', 0],
            [2021, 'Q4', 0],
        ];
        $worksheet->fromArray($dataSet, null, 'A1');
        $worksheet->getAutoFilter()->setRange('A1:C9');

        $reloaded = $this->writeAndReload($spreadsheet, 'Ods');

        self::assertSame('A1:C9', $reloaded->getActiveSheet()->getAutoFilter()->getRange());
    }
}

<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ConditionalPriority2Test extends AbstractFunctional
{
    public function testConditionalPriority(): void
    {
        $filename = 'tests/data/Reader/XLSX/issue.4318.xlsx';
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename);
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $sheet = $reloadedSpreadsheet->getActiveSheet();
        $rules = $sheet->getConditionalStyles('E5', false);
        self::assertCount(4, $rules);
        self::assertSame(2, $rules[0]->getPriority());
        self::assertSame(['$B$2<2'], $rules[0]->getConditions());
        self::assertSame(3, $rules[1]->getPriority());
        self::assertSame(['$B$2=""'], $rules[1]->getConditions());
        self::assertSame(4, $rules[2]->getPriority());
        self::assertSame(['$B$2=3'], $rules[2]->getConditions());
        self::assertSame(5, $rules[3]->getPriority());
        self::assertSame(['$B$2=2'], $rules[3]->getConditions());
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}

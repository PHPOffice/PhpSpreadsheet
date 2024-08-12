<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Issue731Test extends AbstractFunctional
{
    private static string $testbook = 'tests/data/Reader/XLSX/issue.731.xlsx';

    public function testRotateAndFlipImages(): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load(self::$testbook);
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $reloadedSheet = $reloadedSpreadsheet->getActiveSheet();
        $expected = [
            [0, false, false],
            [90, false, false],
            [270, false, false],
            [0, false, true],
            [0, true, false],
            [20, false, false],
            [20, false, true],
            [0, true, true],
        ];
        $actual = [];
        foreach ($reloadedSheet->getDrawingCollection() as $drawing) {
            $actual[] = [$drawing->getRotation(), $drawing->getFlipHorizontal(), $drawing->getFlipVertical()];
        }
        self::assertSame($expected, $actual);
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}

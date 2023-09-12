<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ColourTest extends AbstractFunctional
{
    private \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet;

    protected function setup(): void
    {
        $filename = 'tests/data/Reader/XLS/Colours.xls';
        $reader = new Xls();
        $this->spreadsheet = $reader->load($filename);
    }

    public function testColours(): void
    {
        $colours = [];

        $worksheet = $this->spreadsheet->getActiveSheet();
        for ($row = 1; $row <= 7; ++$row) {
            for ($column = 'A'; $column !== 'J'; ++$column) {
                $cellAddress = "{$column}{$row}";
                $colours[$cellAddress] = $worksheet->getStyle($cellAddress)->getFill()->getStartColor()->getRGB();
            }
        }

        $newSpreadsheet = $this->writeAndReload($this->spreadsheet, 'Xls');
        $newWorksheet = $newSpreadsheet->getActiveSheet();
        foreach ($colours as $cellAddress => $expectedColourValue) {
            $actualColourValue = $newWorksheet->getStyle($cellAddress)->getFill()->getStartColor()->getRGB();
            self::assertSame($expectedColourValue, $actualColourValue);
        }
    }
}

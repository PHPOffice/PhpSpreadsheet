<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class MergedCellsTest extends AbstractFunctional
{
    public static function providerFormats(): array
    {
        return [
            ['Html'],
            ['Xls'],
            ['Xlsx'],
            ['Ods'],
        ];
    }

    /**
     * @dataProvider providerFormats
     */
    public function testMergedCells(string $format): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0);
        $spreadsheet->getActiveSheet()->setCellValue('A1', '1');
        $spreadsheet->getActiveSheet()->setCellValue('B1', '2');
        $spreadsheet->getActiveSheet()->setCellValue('A2', '33');
        $spreadsheet->getActiveSheet()->mergeCells('A2:B2');

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format);

        $actual = 0;
        foreach ($reloadedSpreadsheet->getWorksheetIterator() as $worksheet) {
            $actual += count($worksheet->getMergeCells());
        }

        self::assertSame(1, $actual, "Format $format failed, could not read 1 merged cell");
    }
}

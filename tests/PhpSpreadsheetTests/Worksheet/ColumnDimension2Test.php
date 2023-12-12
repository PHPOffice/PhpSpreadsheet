<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Helper\Dimension;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ColumnDimension2Test extends AbstractFunctional
{
    /**
     * @dataProvider providerType
     */
    public function testSetsAndDefaults(string $type): void
    {
        $columns = ['J', 'A', 'F', 'M', 'N', 'T', 'S'];
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $expectedCm = 0.45;
        foreach ($columns as $column) {
            $sheet->getColumnDimension($column)->setWidth($expectedCm, 'cm');
        }
        if ($type === 'Html') {
            $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $type, null, [self::class, 'inlineCss']);
        } else {
            $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $type);
        }
        $spreadsheet->disconnectWorksheets();
        $sheet = $reloadedSpreadsheet->getActiveSheet();
        for ($column = 'A'; $column !== 'Z'; ++$column) {
            if (in_array($column, $columns, true)) {
                self::assertEqualsWithDelta($expectedCm, $sheet->getColumnDimension($column)->getWidth(Dimension::UOM_CENTIMETERS), 1E-3, "column $column");
            } elseif ($type === 'Xls' && $column <= 'T') {
                // Xls is a bit weird. Columns through max used
                //    actually set a width in the spreadsheet file.
                // Columns above max used obviously do not.
                self::assertEqualsWithDelta(9.140625, $sheet->getColumnDimension($column)->getWidth(), 1E-3, "column $column");
            } elseif ($type === 'Html' && $column <= 'T') {
                // Html is a lot like Xls. Columns through max used
                //    actually set a width in the spreadsheet file.
                // Columns above max used obviously do not.
                self::assertEqualsWithDelta(7.998, $sheet->getColumnDimension($column)->getWidth(), 1E-3, "column $column");
            } else {
                self::assertSame(-1.0, $sheet->getColumnDimension($column)->getWidth(), "column $column");
            }
        }
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public static function providerType(): array
    {
        return [
            ['Xlsx'],
            ['Xls'],
            ['Ods'],
            ['Html'],
        ];
    }

    public static function inlineCss(Html $writer): void
    {
        $writer->setUseInlineCss(true);
    }
}

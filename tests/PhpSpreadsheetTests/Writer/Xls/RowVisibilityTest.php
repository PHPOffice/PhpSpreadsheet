<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xls;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class RowVisibilityTest extends AbstractFunctional
{
    /**
     * @dataProvider dataProviderReoVisibility
     */
    public function testRowVisibility(array $visibleRows): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        foreach ($visibleRows as $row => $visibility) {
            $worksheet->setCellValue("A{$row}", $row);
            $worksheet->getRowDimension($row)->setVisible($visibility);
        }

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xls');
        $reloadedWorksheet = $reloadedSpreadsheet->getActiveSheet();
        foreach ($visibleRows as $row => $visibility) {
            self::assertSame($visibility, $reloadedWorksheet->getRowDimension($row)->getVisible());
        }
    }

    public function dataProviderReoVisibility(): array
    {
        return [
            [
                [1 => true, 2 => false, 3 => false, 4 => true, 5 => true, 6 => false],
            ],
        ];
    }
}

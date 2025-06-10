<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class FontCharsetTest extends AbstractFunctional
{
    private ?Spreadsheet $spreadsheet = null;

    private ?Spreadsheet $reloadedSpreadsheet = null;

    protected function tearDown(): void
    {
        if ($this->spreadsheet !== null) {
            $this->spreadsheet->disconnectWorksheets();
            $this->spreadsheet = null;
        }
        if ($this->reloadedSpreadsheet !== null) {
            $this->reloadedSpreadsheet->disconnectWorksheets();
            $this->reloadedSpreadsheet = null;
        }
    }

    public function testFontCharset(): void
    {
        $spreadsheet = $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->getStyle('A1')->getFont()->setName('Nazanin');
        $spreadsheet->addFontCharset('Nazanin');

        $spreadsheet2 = $this->reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $sheet2 = $spreadsheet2->getActiveSheet();
        $sheet2->getStyle('B1')->getFont()->setName('B Nazanin');
        $sheet2->getStyle('C1')->getFont()->setName('C Nazanin');
        $sheet2->getStyle('D1')->getFont()->setName('D Nazanin');
        $spreadsheet2->addFontCharset('C Nazanin');
        // Do not add D Nazanin for this test.
        self::assertSame(
            [
                'B Nazanin' => 178, // default entry
                'Nazanin' => 178, // should have been set by Xlsx Reader
                'C Nazanin' => 178, // explicitly set in this test
            ],
            $spreadsheet2->getFontCharsets()
        );
    }
}

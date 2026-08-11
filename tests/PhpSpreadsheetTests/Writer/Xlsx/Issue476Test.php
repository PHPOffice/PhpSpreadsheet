<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Issue476Test extends AbstractFunctional
{
    public function testIssue476(): void
    {
        // RichText written in a way usually used only by strings.
        $reader = new XlsxReader();
        $spreadsheet = $reader->load('tests/data/Writer/XLSX/issue.476.xlsx');

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();

        $sheet = $reloadedSpreadsheet->getActiveSheet();
        $richText = $sheet->getCell('A1')->getValue();
        self::assertInstanceOf(RichText::class, $richText);
        $plainText = $richText->getPlainText();
        self::assertSame("Art. 1A of the Geneva Refugee Convention and Protocol or other international or national instruments.\n", $plainText);

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}

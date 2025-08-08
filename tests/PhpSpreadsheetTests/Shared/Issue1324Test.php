<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv as CsvWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Issue1324Test extends AbstractFunctional
{
    protected static int $version = 80400;

    public function testPrecision(): void
    {
        $string1 = '753.149999999999';
        $s1 = (float) $string1;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue($s1);
        self::assertNotSame(753.15, $sheet->getCell('A1')->getValue());
        $formats = ['Csv', 'Xlsx', 'Xls', 'Ods', 'Html'];
        foreach ($formats as $format) {
            $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format);
            $rsheet = $reloadedSpreadsheet->getActiveSheet();
            $s2 = $rsheet->getCell('A1')->getValue();
            self::assertSame($s1, $s2, "difference for $format");
            $reloadedSpreadsheet->disconnectWorksheets();
        }
        $spreadsheet->disconnectWorksheets();
    }

    public function testCsv(): void
    {
        $string1 = '753.149999999999';
        $s1 = (float) $string1;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue($s1);
        $writer = new CsvWriter($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $output = (string) ob_get_clean();
        self::assertStringContainsString($string1, $output);
        $spreadsheet->disconnectWorksheets();
    }
}

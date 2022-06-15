<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Issue2884Test extends AbstractFunctional
{
    public function testChars(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $text = "Text contains: single quote: ' ampersand: & double quote:" . ' " ' . "less than: < greater than: >";
        $sheet->setCellValueByColumnAndRow(1, 1, $text);

        $outputFilename = File::temporaryFilename();
        $writer = new Xlsx($spreadsheet);
        $writer->save($outputFilename);
        $zipFile = "zip://$outputFilename#xl/sharedStrings.xml";
        $contents = file_get_contents($zipFile);
        unlink($outputFilename);
        $spreadsheet->disconnectWorksheets();
        if ($contents === false) {
            self::fail('Unable to open file');
        } else {
            $text = "Text contains: single quote: ' ampersand: &amp; double quote:" . ' " ' . "less than: &lt; greater than: >";
            self::assertTrue(str_contains($contents, $text));
        }
    }
}

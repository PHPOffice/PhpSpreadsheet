<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as Reader;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as Writer;
use PHPUnit\Framework\TestCase;

class CodeNamesRespectedTest extends TestCase
{
    public function testCodeNamesRespected()
    {
        $outputFilename = tempnam(File::sysGetTempDir(), 'phpspreadsheet-test');
        Settings::setLibXmlLoaderOptions(null);

        try {
            $sheet = new Spreadsheet();
            $sheet->getActiveSheet()->setCodeName($expected = 'respecting_code_names');

            $writer = new Writer($sheet);
            $writer->save($outputFilename);

            $reader = new Reader();
            $sheet = $reader->load($outputFilename);

            $this->assertSame($expected, $sheet->getActiveSheet()->getCodeName());
        } finally {
            unlink($outputFilename);
        }
    }
}

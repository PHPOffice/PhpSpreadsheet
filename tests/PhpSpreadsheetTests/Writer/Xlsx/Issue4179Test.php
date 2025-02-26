<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;
use ZipArchive;

class Issue4179Test extends TestCase
{
    private string $outputFile = '';

    protected function tearDown(): void
    {
        if ($this->outputFile !== '') {
            unlink($this->outputFile);
            $this->outputFile = '';
        }
    }

    public function testIssue4179(): void
    {
        // duplicate entry in ContentTypes
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $imageFile = 'tests/data/Writer/XLSX/backgroundtest.png';
        $image = (string) file_get_contents($imageFile);
        $sheet->setBackgroundImage($image);
        $drawing = new Drawing();
        $drawing->setName('Blue Square');
        $drawing->setPath('tests/data/Writer/XLSX/blue_square.png');
        $drawing->setCoordinates('A1');
        $drawing->setWorksheet($sheet);
        $writer = new XlsxWriter($spreadsheet);
        $this->outputFile = File::temporaryFilename();
        $writer->save($this->outputFile);
        $spreadsheet->disconnectWorksheets();

        $zip = new ZipArchive();
        $open = $zip->open($this->outputFile, ZipArchive::RDONLY);
        $pngCount = 0;
        if ($open !== true) {
            self::fail("zip open failed for {$this->outputFile}");
        } else {
            $contents = (string) $zip->getFromName('[Content_Types].xml');
            $subCount = substr_count($contents, '"png"');
            self::assertSame(1, $subCount);
            for ($i = 0; $i < $zip->numFiles; ++$i) {
                $filename = (string) $zip->getNameIndex($i);
                if (preg_match('~^xl/media/\w+[.]png$~', $filename) === 1) {
                    ++$pngCount;
                }
            }
        }
        self::assertSame(2, $pngCount);
    }
}

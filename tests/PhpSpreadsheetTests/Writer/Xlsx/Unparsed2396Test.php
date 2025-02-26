<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as Reader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Worksheet\BaseDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as Writer;
use PHPUnit\Framework\TestCase;

class Unparsed2396Test extends TestCase
{
    private string $filename = '';

    protected function tearDown(): void
    {
        if ($this->filename !== '') {
            unlink($this->filename);
            $this->filename = '';
        }
    }

    private function getContents(?BaseDrawing $drawing): string
    {
        $contents = '';
        if ($drawing instanceof Drawing) {
            $contents = (string) file_get_contents($drawing->getPath());
        } else {
            self::fail('Unexpected null or baseDrawing which is not Drawing');
        }
        self::assertNotSame('', $contents);

        return $contents;
    }

    // Don't drop image as in issue 2396.
    public function testUnparsed2396(): void
    {
        $sampleFilename = 'tests/data/Writer/XLSX/issue.2396.xlsx';
        $reader = new Reader();
        $excel = $reader->load($sampleFilename);
        $outputFilename = $this->filename = File::temporaryFilename();
        $writer = new Writer($excel);
        $writer->save($outputFilename);
        //$spreadsheet = $this->writeAndReload($excel, 'Xlsx');
        $excel->disconnectWorksheets();
        $reader = new Reader();
        $spreadsheet = $reader->load($outputFilename);
        $sheet = $spreadsheet->getSheet(0);
        $drawing1 = $sheet->getDrawingCollection();
        self::assertCount(1, $drawing1);
        $hash = $this->getContents($drawing1[0]);

        $sheet = $spreadsheet->getSheet(1);
        $drawings = $sheet->getDrawingCollection();
        self::assertCount(1, $drawings);
        self::assertSame($hash, $this->getContents($drawings[0]));

        $sheet = $spreadsheet->getSheet(2);
        $drawings = $sheet->getDrawingCollection();
        self::assertCount(0, $drawings);
        //self::assertSame($hash, md5(file_get_contents($drawings[0]->getPath())));

        $sheet = $spreadsheet->getSheet(3);
        $drawings = $sheet->getDrawingCollection();
        self::assertCount(1, $drawings);
        self::assertSame($hash, $this->getContents($drawings[0]));

        $sheet = $spreadsheet->getSheet(4);
        $drawings = $sheet->getDrawingCollection();
        self::assertCount(0, $drawings);
        //self::assertSame($hash, md5(file_get_contents($drawings[0]->getPath())));

        // The next 2 sheets have 'legacyDrawing', button, and listbox.
        // If support is added for those, these tests may need adjustment.
        $sheet = $spreadsheet->getSheet(5);
        $drawings = $sheet->getDrawingCollection();
        self::assertCount(0, $drawings);
        //self::assertSame($hash, md5(file_get_contents($drawings[0]->getPath())));

        $sheet = $spreadsheet->getSheet(6);
        $drawings = $sheet->getDrawingCollection();
        self::assertCount(1, $drawings);
        self::assertSame($hash, $this->getContents($drawings[0]));

        $spreadsheet->disconnectWorksheets();
    }
}

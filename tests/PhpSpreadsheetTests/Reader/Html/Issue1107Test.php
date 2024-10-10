<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Html;

use PhpOffice\PhpSpreadsheet\Reader\Html as HtmlReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use PHPUnit\Framework\TestCase;

class Issue1107Test extends TestCase
{
    private string $outfile = '';

    protected function tearDown(): void
    {
        if ($this->outfile !== '') {
            unlink($this->outfile);
            $this->outfile = '';
        }
    }

    public function testIssue1107(): void
    {
        // failure due to cached file size
        $outstr = str_repeat('a', 1023) . "\n";
        $allout = str_repeat($outstr, 10);
        $this->outfile = $outfile = File::temporaryFilename();
        file_put_contents($outfile, $allout);
        self::assertSame(10240, filesize($outfile));
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue(1);
        $writer = new HtmlWriter($spreadsheet);
        $writer->save($outfile);
        $spreadsheet->disconnectWorksheets();
        $reader = new HtmlReader();
        $spreadsheet2 = $reader->load($outfile);
        $sheet2 = $spreadsheet2->getActiveSheet();
        self::assertSame(1, $sheet2->getCell('A1')->getValue());

        $spreadsheet2->disconnectWorksheets();
    }
}

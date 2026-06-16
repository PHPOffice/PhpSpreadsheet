<?php

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PHPUnit\Framework\TestCase;

class Issue4521Test extends TestCase
{
    private string $outfile = '';

    protected int $weirdMimetypeMajor = 8;

    protected int $weirdMimetypeMinor1 = 1;

    protected int $weirdMimetypeMinor2 = 2;

    protected function tearDown(): void
    {
        if ($this->outfile !== '') {
            unlink($this->outfile);
            $this->outfile = '';
        }
    }

    public function testEmptyFile(): void
    {
        $this->outfile = File::temporaryFilename();
        file_put_contents($this->outfile, '');
        $spreadsheet = IOFactory::load($this->outfile);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('A', $sheet->getHighestColumn());
        self::assertSame(1, $sheet->getHighestRow());
        $spreadsheet->disconnectWorksheets();
    }

    public function testCrlfFile(): void
    {
        if (PHP_MAJOR_VERSION === $this->weirdMimetypeMajor) {
            if (
                PHP_MINOR_VERSION === $this->weirdMimetypeMinor1
                || PHP_MINOR_VERSION === $this->weirdMimetypeMinor2
            ) {
                self::markTestSkipped('Php mimetype bug with this release');
            }
        }
        $this->outfile = File::temporaryFilename();
        file_put_contents($this->outfile, "\r\n");
        $spreadsheet = IOFactory::load($this->outfile);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('A', $sheet->getHighestColumn());
        self::assertSame(1, $sheet->getHighestRow());
        $spreadsheet->disconnectWorksheets();
    }
}

<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Reader\Ods as OdsReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Ods as OdsWriter;
use PHPUnit\Framework\TestCase;

class BooleanDataTest extends TestCase
{
    private string $tempfile = '';

    private string $locale;

    protected function setUp(): void
    {
        $calculation = Calculation::getInstance();
        $this->locale = $calculation->getLocale();
    }

    protected function tearDown(): void
    {
        $calculation = Calculation::getInstance();
        $calculation->setLocale($this->locale);
        if ($this->tempfile !== '') {
            unlink($this->tempfile);
            $this->tempfile = '';
        }
    }

    public function testBooleanData(): void
    {
        $spreadsheetOld = new Spreadsheet();
        $sheetOld = $spreadsheetOld->getActiveSheet();
        $sheetOld->getCell('A1')->setValue(true);
        $sheetOld->getCell('A2')->setValue(false);
        $writer = new OdsWriter($spreadsheetOld);
        $this->tempfile = File::temporaryFileName();
        $writer->save($this->tempfile);
        $spreadsheetOld->disconnectWorksheets();
        $reader = new OdsReader();
        $spreadsheet = $reader->load($this->tempfile);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertTrue($sheet->getCell('A1')->getValue());
        self::assertFalse($sheet->getCell('A2')->getValue());
        $spreadsheet->disconnectWorksheets();
        $zipFile = 'zip://' . $this->tempfile . '#content.xml';
        $contents = (string) file_get_contents($zipFile);
        self::assertStringContainsString('<text:p>TRUE</text:p>', $contents);
        self::assertStringContainsString('<text:p>FALSE</text:p>', $contents);
    }

    public function testBooleanDataGerman(): void
    {
        $calculation = Calculation::getInstance();
        $calculation->setLocale('de');
        $spreadsheetOld = new Spreadsheet();
        $sheetOld = $spreadsheetOld->getActiveSheet();
        $sheetOld->getCell('A1')->setValue(true);
        $sheetOld->getCell('A2')->setValue(false);
        $writer = new OdsWriter($spreadsheetOld);
        $this->tempfile = File::temporaryFileName();
        $writer->save($this->tempfile);
        $spreadsheetOld->disconnectWorksheets();
        $reader = new OdsReader();
        $spreadsheet = $reader->load($this->tempfile);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertTrue($sheet->getCell('A1')->getValue());
        self::assertFalse($sheet->getCell('A2')->getValue());
        $spreadsheet->disconnectWorksheets();
        $zipFile = 'zip://' . $this->tempfile . '#content.xml';
        $contents = (string) file_get_contents($zipFile);
        self::assertStringContainsString('<text:p>WAHR</text:p>', $contents);
        self::assertStringContainsString('<text:p>FALSCH</text:p>', $contents);
        self::assertStringNotContainsString('<text:p>TRUE</text:p>', $contents);
        self::assertStringNotContainsString('<text:p>FALSE</text:p>', $contents);
    }
}

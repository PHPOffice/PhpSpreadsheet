<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;
use ZipArchive;

class Issue4200Test extends TestCase
{
    private string $outputFile = '';

    protected function tearDown(): void
    {
        if ($this->outputFile !== '') {
            unlink($this->outputFile);
            $this->outputFile = '';
        }
    }

    public function testIssue4200(): void
    {
        // ignoredErrors came after legacyDrawing
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValueExplicit('01', DataType::TYPE_STRING);
        $sheet->getCell('A1')
            ->getIgnoredErrors()
            ->setNumberStoredAsText(true);
        $richText = new RichText();
        $richText->createText('hello');
        $sheet->getComment('C1')
            ->setText($richText);
        $writer = new XlsxWriter($spreadsheet);
        $this->outputFile = File::temporaryFilename();
        $writer->save($this->outputFile);
        $spreadsheet->disconnectWorksheets();

        $zip = new ZipArchive();
        $open = $zip->open($this->outputFile, ZipArchive::RDONLY);
        if ($open !== true) {
            self::fail("zip open failed for {$this->outputFile}");
        } else {
            $contents = (string) $zip->getFromName('xl/worksheets/sheet1.xml');
            self::assertStringContainsString(
                '<ignoredErrors><ignoredError sqref="A1" numberStoredAsText="1"/></ignoredErrors><legacyDrawing r:id="rId_comments_vml1"/>',
                $contents
            );
        }
    }

    public function testIssue4145(): void
    {
        // ignoredErrors came after drawing
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValueExplicit('01', DataType::TYPE_STRING);
        $sheet->getCell('A1')
            ->getIgnoredErrors()
            ->setNumberStoredAsText(true);
        $drawing = new Drawing();
        $drawing->setName('Blue Square');
        $drawing->setPath('tests/data/Writer/XLSX/blue_square.png');
        $drawing->setCoordinates('C1');
        $drawing->setWorksheet($sheet);
        $writer = new XlsxWriter($spreadsheet);
        $this->outputFile = File::temporaryFilename();
        $writer->save($this->outputFile);
        $spreadsheet->disconnectWorksheets();

        $zip = new ZipArchive();
        $open = $zip->open($this->outputFile, ZipArchive::RDONLY);
        if ($open !== true) {
            self::fail("zip open failed for {$this->outputFile}");
        } else {
            $contents = (string) $zip->getFromName('xl/worksheets/sheet1.xml');
            self::assertStringContainsString(
                '<ignoredErrors><ignoredError sqref="A1" numberStoredAsText="1"/></ignoredErrors><drawing r:id="rId1"/>',
                $contents
            );
        }
    }
}

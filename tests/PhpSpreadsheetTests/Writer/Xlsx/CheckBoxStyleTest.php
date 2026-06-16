<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

class CheckBoxStyleTest extends TestCase
{
    private string $outputFile = '';

    protected function tearDown(): void
    {
        if ($this->outputFile !== '') {
            unlink($this->outputFile);
            $this->outputFile = '';
        }
    }

    public function testCheckBoxStyle(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', true);
        $sheet->setCellValue('A2', false);
        $sheet->setCellValue('A3', false); // no checkbox
        $sheet->getStyle('A1')->setCheckBox(true);
        $sheet->getStyle('A2')->setCheckBox(true);

        $this->outputFile = File::temporaryFilename();
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($this->outputFile);
        $spreadsheet->disconnectWorksheets();

        $reader = new XlsxReader();
        $reloadedSpreadsheet = $reader->load($this->outputFile);
        $spreadsheet->disconnectWorksheets();

        $rsheet = $reloadedSpreadsheet->getActiveSheet();
        self::assertTrue($rsheet->getStyle('A1')->getCheckBox());
        self::assertTrue($rsheet->getStyle('A2')->getCheckBox());
        self::assertFalse($rsheet->getStyle('A3')->getCheckBox());
        $reloadedSpreadsheet->disconnectWorksheets();

        $file = 'zip://';
        $file .= $this->outputFile;
        $file .= '#[Content_Types].xml';
        $data = file_get_contents($file);
        self::assertNotFalse($data);
        self::assertStringContainsString(
            'featurePropertyBag',
            $data
        );

        $file = 'zip://';
        $file .= $this->outputFile;
        $file .= '#xl/_rels/workbook.xml.rels';
        $data = file_get_contents($file);
        self::assertNotFalse($data);
        self::assertStringContainsString(
            'featurePropertyBag',
            $data
        );

        $file = 'zip://';
        $file .= $this->outputFile;
        $file .= '#xl/featurePropertyBag/featurePropertyBag.xml';
        $data = file_get_contents($file);
        self::assertNotFalse($data);
        self::assertStringContainsString(
            'Checkbox',
            $data
        );
    }

    public function testCheckBoxStyleDiskCache(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', true);
        $sheet->setCellValue('A2', false);
        $sheet->setCellValue('A3', false); // no checkbox
        $sheet->getStyle('A1')->setCheckBox(true);
        $sheet->getStyle('A2')->setCheckBox(true);

        $this->outputFile = File::temporaryFilename();
        $writer = new XlsxWriter($spreadsheet);
        $writer->setUseDiskCaching(true, sys_get_temp_dir());
        $writer->save($this->outputFile);
        $spreadsheet->disconnectWorksheets();

        $reader = new XlsxReader();
        $reloadedSpreadsheet = $reader->load($this->outputFile);
        $spreadsheet->disconnectWorksheets();

        $rsheet = $reloadedSpreadsheet->getActiveSheet();
        self::assertTrue($rsheet->getStyle('A1')->getCheckBox());
        self::assertTrue($rsheet->getStyle('A2')->getCheckBox());
        self::assertFalse($rsheet->getStyle('A3')->getCheckBox());
        $reloadedSpreadsheet->disconnectWorksheets();

        $file = 'zip://';
        $file .= $this->outputFile;
        $file .= '#[Content_Types].xml';
        $data = file_get_contents($file);
        self::assertNotFalse($data);
        self::assertStringContainsString(
            'featurePropertyBag',
            $data
        );

        $file = 'zip://';
        $file .= $this->outputFile;
        $file .= '#xl/_rels/workbook.xml.rels';
        $data = file_get_contents($file);
        self::assertNotFalse($data);
        self::assertStringContainsString(
            'featurePropertyBag',
            $data
        );

        $file = 'zip://';
        $file .= $this->outputFile;
        $file .= '#xl/featurePropertyBag/featurePropertyBag.xml';
        $data = file_get_contents($file);
        self::assertNotFalse($data);
        self::assertStringContainsString(
            'Checkbox',
            $data
        );
    }
}

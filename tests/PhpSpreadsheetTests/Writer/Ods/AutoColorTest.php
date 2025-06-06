<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\ODS;

use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Ods as OdsWriter;
use PHPUnit\Framework\TestCase;

class AutoColorTest extends TestCase
{
    private string $outputFile = '';

    protected function tearDown(): void
    {
        if ($this->outputFile !== '') {
            unlink($this->outputFile);
            $this->outputFile = '';
        }
    }

    public function testAutoColor(): void
    {
        // It's not clear to me what AutoColor does in Excel.
        // However, LibreOffice Dark Mode
        // can make use of a spreadsheet which uses it.
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getDefaultStyle()->getFont()
            ->setAutoColor(true);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Hello World!');
        $sheet->setCellValue('A2', 'Hello World!');
        $sheet->getStyle('A2')->getFont()
            ->setBold(true);
        $sheet->setCellValue('A3', 'Hello World!');
        $sheet->getStyle('A3')->getFont()
            ->setItalic(true);
        $sheet->setCellValue('B1', 'Hello World!');

        $writer = new OdsWriter($spreadsheet);
        $outputFile = $this->outputFile = File::temporaryFilename();
        $writer->save($outputFile);
        $spreadsheet->disconnectWorksheets();
        $zipfile = "zip://$outputFile#content.xml";
        $contents = file_get_contents($zipfile);
        if ($contents === false) {
            self::fail('Unable to open file');
        } else {
            self::assertStringContainsString('<style:text-properties style:use-window-font-color="true" fo:font-family="Calibri" fo:font-size="11.0pt"/>', $contents);
            self::assertStringContainsString('<style:text-properties fo:font-weight="bold" style:font-weight-complex="bold" style:font-weight-asian="bold" style:use-window-font-color="true" fo:font-family="Calibri" fo:font-size="11.0pt"/>', $contents);
            self::assertStringContainsString('<style:text-properties fo:font-style="italic" style:use-window-font-color="true" fo:font-family="Calibri" fo:font-size="11.0pt"/>', $contents);
        }
    }
}

<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
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

        $writer = new XlsxWriter($spreadsheet);
        $outputFile = $this->outputFile = File::temporaryFilename();
        $writer->save($outputFile);
        $spreadsheet->disconnectWorksheets();
        $zipfile = "zip://$outputFile#xl/styles.xml";
        $contents = file_get_contents($zipfile);
        if ($contents === false) {
            self::fail('Unable to open file');
        } else {
            self::assertStringContainsString('<fonts count="3">', $contents);
            self::assertStringContainsString('<font><b val="0"/><i val="0"/><strike val="0"/><u val="none"/><sz val="11"/><auto val="1"/><name val="Calibri"/></font>', $contents);
            self::assertStringContainsString('<font><b val="1"/><i val="0"/><strike val="0"/><u val="none"/><sz val="11"/><auto val="1"/><name val="Calibri"/></font>', $contents);
            self::assertStringContainsString('<font><b val="0"/><i val="1"/><strike val="0"/><u val="none"/><sz val="11"/><auto val="1"/><name val="Calibri"/></font>', $contents);
        }

        $reader = new XlsxReader();
        $spreadsheet2 = $reader->load($outputFile);
        $sheet2 = $spreadsheet2->getActiveSheet();
        self::assertTrue(
            $sheet2->getStyle('A1')
                ->getFont()
                ->getAutoColor()
        );
        $spreadsheet2->disconnectWorksheets();
    }
}

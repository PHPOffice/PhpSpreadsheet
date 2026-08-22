<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

class ExplicitStyle0Test extends TestCase
{
    private string $outputFile = '';

    protected function tearDown(): void
    {
        if ($this->outputFile !== '') {
            unlink($this->outputFile);
            $this->outputFile = '';
        }
    }

    public function testWithoutExplicitStyle0(): void
    {
        $spreadsheet = new Spreadsheet();
        $defaultStyle = $spreadsheet->getDefaultStyle();
        $defaultStyle->getFont()->setBold(true);
        $defaultStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue('bold');
        $sheet->getCell('A2')->setValue('italic');
        $sheet->getStyle('A2')->getFont()->setItalic(true);
        $writer = new XlsxWriter($spreadsheet);
        $this->outputFile = File::temporaryFilename();
        $writer->save($this->outputFile);
        $spreadsheet->disconnectWorksheets();

        $reader = new XlsxReader();
        $spreadsheet2 = $reader->load($this->outputFile);
        $sheet2 = $spreadsheet2->getActiveSheet();
        self::assertTrue($sheet2->getStyle('A1')->getFont()->getBold());
        self::assertFalse($sheet2->getStyle('A1')->getFont()->getItalic());
        self::assertSame(Alignment::HORIZONTAL_CENTER, $sheet2->getStyle('A1')->getAlignment()->getHorizontal());
        self::assertTrue($sheet2->getStyle('A2')->getFont()->getBold());
        self::assertTrue($sheet2->getStyle('A2')->getFont()->getItalic());
        self::assertSame(Alignment::HORIZONTAL_CENTER, $sheet2->getStyle('A2')->getAlignment()->getHorizontal());
        $spreadsheet2->disconnectWorksheets();

        $file = 'zip://';
        $file .= $this->outputFile;
        $file .= '#xl/worksheets/sheet1.xml';
        $data = file_get_contents($file);
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            self::assertStringContainsString('<c r="A1" t="s"><v>0</v></c>', $data, 'no s attribute in c tag');
            self::assertStringContainsString('<c r="A2" s="1" t="s"><v>1</v></c>', $data);
        }
    }

    public function testWithExplicitStyle0(): void
    {
        $spreadsheet = new Spreadsheet();
        $defaultStyle = $spreadsheet->getDefaultStyle();
        $defaultStyle->getFont()->setBold(true);
        $defaultStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue('bold');
        $sheet->getCell('A2')->setValue('italic');
        $sheet->getStyle('A2')->getFont()->setItalic(true);
        $writer = new XlsxWriter($spreadsheet);
        $writer->setExplicitStyle0(true);
        $this->outputFile = File::temporaryFilename();
        $writer->save($this->outputFile);
        $spreadsheet->disconnectWorksheets();

        $reader = new XlsxReader();
        $spreadsheet2 = $reader->load($this->outputFile);
        $sheet2 = $spreadsheet2->getActiveSheet();
        self::assertTrue($sheet2->getStyle('A1')->getFont()->getBold());
        self::assertFalse($sheet2->getStyle('A1')->getFont()->getItalic());
        self::assertSame(Alignment::HORIZONTAL_CENTER, $sheet2->getStyle('A1')->getAlignment()->getHorizontal());
        self::assertTrue($sheet2->getStyle('A2')->getFont()->getBold());
        self::assertTrue($sheet2->getStyle('A2')->getFont()->getItalic());
        self::assertSame(Alignment::HORIZONTAL_CENTER, $sheet2->getStyle('A2')->getAlignment()->getHorizontal());
        $spreadsheet2->disconnectWorksheets();

        $file = 'zip://';
        $file .= $this->outputFile;
        $file .= '#xl/worksheets/sheet1.xml';
        $data = file_get_contents($file);
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            self::assertStringContainsString('<c r="A1" s="0" t="s"><v>0</v></c>', $data, 'has s attribute in c tag');
            self::assertStringContainsString('<c r="A2" s="1" t="s"><v>1</v></c>', $data);
        }
    }
}

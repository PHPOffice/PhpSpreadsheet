<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as Reader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as Writer;
use PHPUnit\Framework\TestCase;
use ZipArchive;

class StartsWithHashTest extends TestCase
{
    public function testStartWithHash(): void
    {
        $outputFilename = File::temporaryFilename();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValueExplicit('A1', '#define M', DataType::TYPE_STRING);
        $sheet->setCellValue('A2', '=A1');
        $sheet->setCellValue('A3', '=UNKNOWNFUNC()');

        $writer = new Writer($spreadsheet);
        $writer->save($outputFilename);

        $reader = new Reader();
        $sheet = $reader->load($outputFilename);
        unlink($outputFilename);

        self::assertSame('#define M', $sheet->getActiveSheet()->getCell('A1')->getValue());
        self::assertSame('#define M', $sheet->getActiveSheet()->getCell('A2')->getCalculatedValue());
        self::assertSame('f', $sheet->getActiveSheet()->getCell('A3')->getDataType());
        self::assertSame('#NAME?', $sheet->getActiveSheet()->getCell('A3')->getCalculatedValue());
        self::assertSame('f', $sheet->getActiveSheet()->getCell('A3')->getDataType());
    }

    public function testStartWithHashReadRaw(): void
    {
        // Make sure raw data indicates A3 is an error, but A2 isn't.
        $outputFilename = File::temporaryFilename();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValueExplicit('A1', '#define M', DataType::TYPE_STRING);
        $sheet->setCellValue('A2', '=A1');
        $sheet->setCellValue('A3', '=UNKNOWNFUNC()');

        $writer = new Writer($spreadsheet);
        $writer->save($outputFilename);
        $zip = new ZipArchive();
        $zip->open($outputFilename);
        $resultSheet1Raw = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        unlink($outputFilename);

        self::assertStringContainsString('<c r="A3" t="e">', $resultSheet1Raw);
        self::assertStringContainsString('<c r="A2" t="str">', $resultSheet1Raw);
    }
}

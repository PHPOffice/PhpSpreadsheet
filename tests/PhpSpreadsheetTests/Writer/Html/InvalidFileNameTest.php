<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PhpOffice\PhpSpreadsheetTests\Functional;

class InvalidFileNameTest extends Functional\AbstractFunctional
{
    public function testEmptyFileName()
    {
        self::expectException(WriterException::class);
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->getCell('A1')->setValue('Cell 1');
        $writer = new Html($spreadsheet);
        @$writer->save('');
    }

    public function testEmptyFileNamePdf()
    {
        self::expectException(WriterException::class);
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->getCell('A1')->setValue('Cell 1');
        $writer = IOFactory::createWriter($spreadsheet, 'Mpdf');
        @$writer->save('');
    }

    public function testEmptyTempdirNamePdf()
    {
        self::expectException(WriterException::class);
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->getCell('A1')->setValue('Cell 1');
        $writer = IOFactory::createWriter($spreadsheet, 'Mpdf');
        $writer->setFont('Helvetica');
        self::assertEquals('Helvetica', $writer->getFont());
        $writer->setPaperSize(PageSetup::PAPERSIZE_LEDGER);
        self::assertEquals($writer->getPaperSize(), PageSetup::PAPERSIZE_LEDGER);
        self::assertEquals(File::sysGetTempDir(), $writer->getTempDir());
        $writer->setTempDir('');
    }

    public function testWinFileNames()
    {
        self::assertEquals('file:///C:/temp/filename.xlsx', Html::winFileToUrl('C:\\temp\filename.xlsx'));
        self::assertEquals('/tmp/filename.xlsx', Html::winFileToUrl('/tmp/filename.xlsx'));
        self::assertEquals('a:bfile', Html::winFileToUrl('a:bfile'));
    }
}

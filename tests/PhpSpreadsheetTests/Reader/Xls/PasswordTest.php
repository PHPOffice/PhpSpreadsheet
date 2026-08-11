<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\Xls as XlsReader;
use PHPUnit\Framework\TestCase;

class PasswordTest extends TestCase
{
    public function testDefaultPassword(): void
    {
        $filename = 'tests/data/Reader/XLS/pwtest.xls';
        $reader = new XlsReader();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('x', $sheet->getCell('A1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testWrongPassword(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Decryption password incorrect');
        $filename = 'tests/data/Reader/XLS/pwtest2.xls';
        $reader = new XlsReader();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('test2', $sheet->getCell('A1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testCorrectPassword(): void
    {
        $filename = 'tests/data/Reader/XLS/pwtest2.xls';
        $reader = new XlsReader();
        $reader->setEncryptionPassword('pwtest2');
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('test2', $sheet->getCell('A1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testUnsupportedEncryption(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Unsupported encryption algorithm');
        $filename = 'tests/data/Reader/XLS/pwtest3.xls';
        $reader = new XlsReader();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('test2', $sheet->getCell('A1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }
}

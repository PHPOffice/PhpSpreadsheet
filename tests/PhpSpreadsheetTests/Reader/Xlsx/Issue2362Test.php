<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PHPUnit\Framework\TestCase;
use ZipArchive;

class Issue2362Test extends TestCase
{
    public function testPreliminaries(): void
    {
        // ZipArchive says file is 'inconsistent',
        // but Excel has no problem with it.
        $filename = 'tests/data/Reader/XLSX/issue.2362.xlsx';
        $zip = new ZipArchive();
        $res = $zip->open($filename, ZipArchive::CHECKCONS);
        self::assertSame(ZipArchive::ER_INCONS, $res);
    }

    public function testIssue2362(): void
    {
        $filename = 'tests/data/Reader/XLSX/issue.2362.xlsx';
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();

        $value = $sheet->getCell('A1')->getValue();
        if ($value instanceof RichText) {
            self::assertSame('Дата', (string) $value);
        } else {
            self::fail('A1 is not RichText');
        }
        $value = $sheet->getCell('D21')->getValue();
        if ($value instanceof RichText) {
            self::assertSame('391800, Рязанская область, г. Скопин, ул. Ленина, д. 40', (string) $value);
        } else {
            self::fail('D21 is not RichText');
        }
        $spreadsheet->disconnectWorksheets();
    }
}

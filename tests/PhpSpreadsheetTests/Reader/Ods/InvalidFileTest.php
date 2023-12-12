<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PHPUnit\Framework\TestCase;

class InvalidFileTest extends TestCase
{
    public function testInvalidFileLoad(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Could not find zip member');
        $temp = __FILE__;
        $reader = new Ods();
        $reader->load($temp);
    }

    public function testInvalidFileNames(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Could not find zip member');
        $temp = __FILE__;
        $reader = new Ods();
        $reader->listWorksheetNames($temp);
    }

    public function testInvalidInfo(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Could not find zip member');
        $temp = __FILE__;
        $reader = new Ods();
        $reader->listWorksheetInfo($temp);
    }

    public function testXlsxFileLoad(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Could not find zip member');
        $temp = 'samples/templates/26template.xlsx';
        $reader = new Ods();
        $reader->load($temp);
    }

    public function testXlsxFileNames(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Could not find zip member');
        $temp = 'samples/templates/26template.xlsx';
        $reader = new Ods();
        $reader->listWorksheetNames($temp);
    }

    public function testXlsxInfo(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Could not find zip member');
        $temp = 'samples/templates/26template.xlsx';
        $reader = new Ods();
        $reader->listWorksheetInfo($temp);
    }
}

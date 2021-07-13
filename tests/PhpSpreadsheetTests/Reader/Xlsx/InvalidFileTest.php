<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PHPUnit\Framework\TestCase;

class InvalidFileTest extends TestCase
{
    public function testInvalidFileLoad(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Could not find zip member');
        $temp = __FILE__;
        $reader = new Xlsx();
        $reader->load($temp);
    }

    public function testInvalidFileNames(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Could not find zip member');
        $temp = __FILE__;
        $reader = new Xlsx();
        $reader->listWorksheetNames($temp);
    }

    public function testInvalidInfo(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Could not find zip member');
        $temp = __FILE__;
        $reader = new Xlsx();
        $reader->listWorksheetInfo($temp);
    }

    public function testOdsFileLoad(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Could not find zip member');
        $temp = 'samples/templates/OOCalcTest.ods';
        $reader = new Xlsx();
        $reader->load($temp);
    }

    public function testOdsFileNames(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Could not find zip member');
        $temp = 'samples/templates/OOCalcTest.ods';
        $reader = new Xlsx();
        $reader->listWorksheetNames($temp);
    }

    public function testOdsInfo(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Could not find zip member');
        $temp = 'samples/templates/OOCalcTest.ods';
        $reader = new Xlsx();
        $reader->listWorksheetInfo($temp);
    }
}

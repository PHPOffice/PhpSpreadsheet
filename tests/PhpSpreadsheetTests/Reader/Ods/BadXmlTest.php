<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\Ods as OdsReader;
use PHPUnit\Framework\TestCase;

class BadXmlTest extends TestCase
{
    private const DIRECTORY = 'tests/data/Reader/Ods/';

    public function testBadStyle(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Opening and ending tag mismatch');
        $fileName = self::DIRECTORY . 'badstyle.ods';
        $reader = new OdsReader();
        $reader->load($fileName);
    }

    public function testBadContent(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Opening and ending tag mismatch');
        $fileName = self::DIRECTORY . 'badcontent.ods';
        $reader = new OdsReader();
        $reader->load($fileName);
    }

    public function testBadSettings(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Premature end of data');
        $fileName = self::DIRECTORY . 'badsettings.ods';
        $reader = new OdsReader();
        $reader->load($fileName);
    }
}

<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PHPUnit\Framework\Attributes;
use PHPUnit\Framework\TestCase;

// All tests must run in separate process due to ini_set
#[Attributes\RunTestsInSeparateProcesses]
class Sec36jjTest extends TestCase
{
    private const XLSX_FILE = 'tests/data/Reader/XLSX/sec36jj.bomb-rels.xlsx';

    protected function setUp(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('over the limit');
        ini_set('memory_limit', '128M');
    }

    public function testWorksheetInfo(): void
    {
        $reader = new XlsxReader();
        $reader->listWorksheetInfo(self::XLSX_FILE);
    }

    public function testWorksheetNames(): void
    {
        $reader = new XlsxReader();
        $reader->listWorksheetNames(self::XLSX_FILE);
    }

    public function testCanRead(): void
    {
        $reader = new XlsxReader();
        $reader->canRead(self::XLSX_FILE);
    }

    public function testLoad(): void
    {
        $reader = new XlsxReader();
        $reader->load(self::XLSX_FILE);
    }

    public function testIdentify(): void
    {
        IOFactory::identify(self::XLSX_FILE);
    }
}

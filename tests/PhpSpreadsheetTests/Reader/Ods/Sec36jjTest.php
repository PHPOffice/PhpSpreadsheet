<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\Ods as OdsReader;
use PHPUnit\Framework\TestCase;

/**
 * All tests must run in separate process due to ini_set.
 *
 * @runTestsInSeparateProcesses
 */
class Sec36jjTest extends TestCase
{
    private const ODS_FILE = 'tests/data/Reader/Ods/sec36jj.bomb-rels.ods';

    protected function setUp(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('over the limit');
        ini_set('memory_limit', '128M');
    }

    public function testWorksheetInfo(): void
    {
        $reader = new OdsReader();
        $reader->listWorksheetInfo(self::ODS_FILE);
    }

    public function testWorksheetNames(): void
    {
        $reader = new OdsReader();
        $reader->listWorksheetNames(self::ODS_FILE);
    }

    // Ods canRead is less thorough than Xlsx - Ods passes
    public function xtestCanRead(): void
    {
        $reader = new OdsReader();
        $reader->canRead(self::ODS_FILE);
    }

    public function testLoad(): void
    {
        $reader = new OdsReader();
        $reader->load(self::ODS_FILE);
    }

    // Ods identify is less thorough than Xlsx - Ods passes
    public function xtestIdentify(): void
    {
        IOFactory::identify(self::ODS_FILE);
    }
}

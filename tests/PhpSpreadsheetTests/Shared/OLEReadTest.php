<?php

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Shared\OLERead;
use PHPUnit\Framework\TestCase;

class OLEReadTest extends TestCase
{
    public function testReadOleStreams()
    {
        $dataDir = './data/Shared/OLERead/';
        $ole = new OLERead();
        $ole->read('./data/Reader/XLS/sample.xls');
        self::assertEquals(
            file_get_contents($dataDir . 'wrkbook'),
            $ole->getStream($ole->wrkbook)
        );
        self::assertEquals(
            file_get_contents($dataDir . 'summary'),
            $ole->getStream($ole->summaryInformation)
        );
        self::assertEquals(
            file_get_contents($dataDir . 'document'),
            $ole->getStream($ole->documentSummaryInformation)
        );
    }
}

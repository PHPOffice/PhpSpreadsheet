<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Shared\OLE;
use PHPUnit\Framework\TestCase;
use Throwable;

class OLETest extends TestCase
{
    public function testReadNotOle(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('File doesn\'t seem to be an OLE container.');
        $ole = new OLE();
        $ole->read(__FILE__);
    }

    public function testReadNotExist(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Can\'t open file');
        $ole = new OLE();
        $ole->read(__FILE__ . '.xxx');
    }

    public function testReadOleStreams(): void
    {
        $dataDir = 'tests/data/Shared/OLERead/';
        $ole = new OLE();
        $oleData = $ole->read('tests/data/Reader/XLS/sample.xls');
        self::assertEquals(
            file_get_contents($dataDir . 'wrkbook'),
            $oleData
        );
        self::assertSame(512, $ole->bigBlockSize);
        self::assertSame(64, $ole->smallBlockSize);
        self::assertSame(4096, $ole->bigBlockThreshold);
        self::assertSame(1024, $ole->getBlockOffset(1));
    }

    public function testChainedWriteMode(): void
    {
        $ole = new OLE\ChainedBlockStream();
        $openedPath = '';
        self::assertFalse($ole->stream_open('whatever', 'w', 0, $openedPath));

        // Test moved to OLEPhpunit10Test for PhpUnit 10
        if (method_exists($this, 'setOutputCallback')) {
            try {
                $ole->stream_open('whatever', 'w', STREAM_REPORT_ERRORS, $openedPath);
                self::fail('Error in statement above should be caught');
            } catch (Throwable $e) {
                self::assertSame('Only reading is supported', $e->getMessage());
            }
        }
    }

    public function testChainedBadPath(): void
    {
        $ole = new OLE\ChainedBlockStream();
        $openedPath = '';
        self::assertFalse($ole->stream_open('whatever', 'r', 0, $openedPath));

        // Not sure how to do this test with PhpUnit 10
        if (method_exists($this, 'setOutputCallback')) {
            try {
                $ole->stream_open('whatever', 'r', STREAM_REPORT_ERRORS, $openedPath);
                self::fail('Error in statement above should be caught');
            } catch (Throwable $e) {
                self::assertSame('OLE stream not found', $e->getMessage());
            }
        }
    }
}

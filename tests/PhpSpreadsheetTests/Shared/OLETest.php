<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Shared\OLE;
use PHPUnit\Framework\TestCase;

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

    // testChainedWriteMode moved to OLEPhpunit10Test
    // testChainedBadPath moved to OLEPhpunit10Test

    public function testOleFunctions(): void
    {
        $ole = new OLE();
        $infile = 'tests/data/Reader/XLS/pr.4687.excel.xls';
        $ole->read($infile);
        self::assertSame(4, $ole->ppsTotal());
        self::assertFalse($ole->isFile(0), 'root entry');
        self::assertTrue($ole->isFile(1), 'workbook');
        self::assertTrue($ole->isFile(2), 'summary information');
        self::assertTrue($ole->isFile(3), 'document summary information');
        self::assertFalse($ole->isFile(4), 'no such index');
        self::assertTrue($ole->isRoot(0), 'root entry');
        self::assertFalse($ole->isRoot(1), 'workbook');
        self::assertFalse($ole->isRoot(2), 'summary information');
        self::assertFalse($ole->isRoot(3), 'document summary information');
        self::assertFalse($ole->isRoot(4), 'no such index');
        self::assertSame(0, $ole->getDataLength(0), 'root entry');
        self::assertSame(15712, $ole->getDataLength(1), 'workbook');
        self::assertSame(4096, $ole->getDataLength(2), 'summary information');
        self::assertSame(4096, $ole->getDataLength(3), 'document summary information');
        self::assertSame(0, $ole->getDataLength(4), 'no such index');
        self::assertSame('', $ole->getData(2, -1, 4), 'negative position');
        self::assertSame('', $ole->getData(2, 5000, 4), 'position > length');
        self::assertSame('feff0000', bin2hex($ole->getData(2, 0, 4)));
        self::assertSame('', $ole->getData(4, 0, 4), 'no such index');
    }

    public function testBadEndian(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Only Little-Endian encoding is supported');
        $ole = new OLE();
        $infile = 'tests/data/Reader/XLS/pr.4687.excel.badendian.xls';
        $ole->read($infile);
    }
}

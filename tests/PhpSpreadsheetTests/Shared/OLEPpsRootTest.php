<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Reader\Xls as XlsReader;
use PhpOffice\PhpSpreadsheet\Shared\OLE;
use PhpOffice\PhpSpreadsheet\Shared\OLE\PPS\File;
use PhpOffice\PhpSpreadsheet\Shared\OLE\PPS\Root;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls as XlsWriter;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class OLEPpsRootTest extends TestCase
{
    public function testWritesVersion3Header(): void
    {
        $file = tmpfile();
        self::assertNotFalse($file);

        try {
            self::assertTrue((new Root(null, null, []))->save($file));
            rewind($file);
            $header = stream_get_contents($file, 512);

            self::assertSame(0x003E, self::headerInteger('v', $header, 24));
            self::assertSame(0x0003, self::headerInteger('v', $header, 26));
            self::assertSame(9, self::headerInteger('v', $header, 30));
            self::assertSame(6, self::headerInteger('v', $header, 32));
        } finally {
            fclose($file);
        }
    }

    public function testKeeps109FatSectorsInTheHeaderDifat(): void
    {
        $stream = new File('LargeStream');
        $stream->append(str_repeat('x', 13716 * 512));
        $file = tmpfile();
        self::assertNotFalse($file);

        try {
            self::assertTrue((new Root(null, null, [$stream]))->save($file));
            rewind($file);
            $header = stream_get_contents($file, 512);

            self::assertSame(109, self::headerInteger('V', $header, 44));
            self::assertSame(0xFFFFFFFE, self::headerInteger('V', $header, 68));
            self::assertSame(0, self::headerInteger('V', $header, 72));
        } finally {
            fclose($file);
        }
    }

    public function testCreatesDifatSectorFor110FatSectors(): void
    {
        $stream = new File('LargeStream');
        $stream->append(str_repeat('x', 13843 * 512));
        $file = tmpfile();
        self::assertNotFalse($file);

        try {
            self::assertTrue((new Root(null, null, [$stream]))->save($file));
            rewind($file);
            $header = stream_get_contents($file, 512);

            self::assertSame(110, self::headerInteger('V', $header, 44));
            self::assertSame(13954, self::headerInteger('V', $header, 68));
            self::assertSame(1, self::headerInteger('V', $header, 72));
            fseek($file, (13954 + 1) * 512);
            $difat = stream_get_contents($file, 512);
            self::assertSame(13953, self::headerInteger('V', $difat, 0));
            self::assertSame(0xFFFFFFFE, self::headerInteger('V', $difat, 508));
        } finally {
            fclose($file);
        }
    }

    public function testCreatesTwoDifatSectorsFor237FatSectors(): void
    {
        $stream = new File('LargeStream');
        $stream->append(str_repeat('x', 30096 * 512));
        $file = tmpfile();
        self::assertNotFalse($file);

        try {
            self::assertTrue((new Root(null, null, [$stream]))->save($file));
            rewind($file);
            $header = stream_get_contents($file, 512);

            self::assertSame(237, self::headerInteger('V', $header, 44));
            self::assertSame(30334, self::headerInteger('V', $header, 68));
            self::assertSame(2, self::headerInteger('V', $header, 72));

            fseek($file, (30334 + 1) * 512);
            $firstDifat = stream_get_contents($file, 512);
            self::assertSame(30206, self::headerInteger('V', $firstDifat, 0));
            self::assertSame(30335, self::headerInteger('V', $firstDifat, 508));

            $secondDifat = stream_get_contents($file, 512);
            self::assertSame(30333, self::headerInteger('V', $secondDifat, 0));
            self::assertSame(0xFFFFFFFE, self::headerInteger('V', $secondDifat, 508));
        } finally {
            fclose($file);
        }
    }

    public function testRejectsStreamsLargerThanTheVersion3Limit(): void
    {
        $stream = new class ('TooLarge') extends File {
            public function getDataLen(): int
            {
                return 0x80000001;
            }
        };
        $file = tmpfile();
        self::assertNotFalse($file);

        try {
            $this->expectException(Exception::class);
            $this->expectExceptionMessage('OLE version-3 streams cannot exceed 2 GiB.');
            (new Root(null, null, [$stream]))->save($file);
        } finally {
            fclose($file);
        }
    }

    public function testAcceptsStreamAtTheVersion3Limit(): void
    {
        $method = new ReflectionMethod(Root::class, 'assertVersion3StreamSize');

        self::assertNull($method->invoke(new Root(null, null, []), 0x80000000));
    }

    public function testRejectsOversizedAggregateMiniStreamBeforeWriting(): void
    {
        $method = new ReflectionMethod(Root::class, 'assertVersion3MiniStreamSize');
        $root = new Root(null, null, []);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('OLE version-3 streams cannot exceed 2 GiB.');
        $method->invoke($root, intdiv(0x80000000, 64) + 1);
    }

    public function testStoresStreamsAtTheMiniStreamBoundary(): void
    {
        $this->assertStreamStorage(4095, 0, 1);
        $this->assertStreamStorage(4096, 0xFFFFFFFE, 0);
    }

    public function testXlsWriterProducesReadableVersion3Cfb(): void
    {
        $file = tmpfile();
        self::assertNotFalse($file);
        $metadata = stream_get_meta_data($file);
        self::assertArrayHasKey('uri', $metadata);
        self::assertIsString($metadata['uri']);
        $path = $metadata['uri'];
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->setCellValue('A1', 'CFB v3');

        try {
            (new XlsWriter($spreadsheet))->save($path);
            $header = file_get_contents($path, false, null, 0, 512);
            self::assertIsString($header);
            self::assertSame(0x003E, self::headerInteger('v', $header, 24));
            self::assertSame(9, self::headerInteger('v', $header, 30));
            self::assertSame(6, self::headerInteger('v', $header, 32));

            $result = (new XlsReader())->load($path);
            self::assertSame('CFB v3', $result->getActiveSheet()->getCell('A1')->getValue());
            $result->disconnectWorksheets();
        } finally {
            $spreadsheet->disconnectWorksheets();
            fclose($file);
        }
    }

    private function assertStreamStorage(int $length, int $firstMiniFatSector, int $miniFatSectorCount): void
    {
        $data = str_repeat('x', $length);
        $stream = new File('BoundaryStream');
        $stream->append($data);
        $file = tmpfile();
        self::assertNotFalse($file);
        $metadata = stream_get_meta_data($file);
        self::assertArrayHasKey('uri', $metadata);
        self::assertIsString($metadata['uri']);
        $path = $metadata['uri'];

        try {
            self::assertTrue((new Root(null, null, [$stream]))->save($file));
            rewind($file);
            $header = stream_get_contents($file, 512);
            self::assertSame($firstMiniFatSector, self::headerInteger('V', $header, 60));
            self::assertSame($miniFatSectorCount, self::headerInteger('V', $header, 64));

            $ole = new OLE();
            $ole->read($path);
            self::assertSame(2, $ole->ppsTotal());
            self::assertTrue($ole->isFile(1));
            self::assertSame($data, self::getDataByName($ole, 'BoundaryStream'));
        } finally {
            fclose($file);
        }
    }

    private static function headerInteger(string $format, string $header, int $offset): int
    {
        $value = unpack($format . 'value', substr($header, $offset));
        self::assertIsArray($value);
        self::assertArrayHasKey('value', $value);
        self::assertIsInt($value['value']);

        return $value['value'];
    }

    private static function getDataByName(OLE $ole, string $name): string
    {
        foreach ($ole->_list as $index => $pps) {
            if ($pps->Type === OLE::OLE_PPS_TYPE_FILE && $pps->Name === $name) {
                return $ole->getData($index, 0, $pps->Size);
            }
        }

        self::fail("OLE stream '$name' was not found.");
    }
}

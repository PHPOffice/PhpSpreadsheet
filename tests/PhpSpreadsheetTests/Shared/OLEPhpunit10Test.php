<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Shared\OLE;
use PhpOffice\PhpSpreadsheet\Shared\OLE\PPS\Root;
use PHPUnit\Framework\TestCase;

/**
 * There were problems running these tests in OLETest with PhpUnit 10.
 * These replacements seem to work.
 */
class OLEPhpunit10Test extends TestCase
{
    private static string $errorString;

    protected function setUp(): void
    {
        self::$errorString = '';
        set_error_handler([self::class, 'errorHandler']);
    }

    protected function tearDown(): void
    {
        restore_error_handler();
    }

    public static function errorHandler(int $errno, string $errstr): bool
    {
        if ($errno === E_USER_WARNING) {
            self::$errorString = $errstr;

            return true; // stop error handling
        }

        return false; // continue error handling
    }

    public function testChainedWriteMode(): void
    {
        self::assertSame('', self::$errorString);
        $ole = new OLE\ChainedBlockStream();
        $openedPath = '';
        self::assertFalse($ole->stream_open('whatever', 'w', 0, $openedPath));

        $ole->stream_open('whatever', 'w', STREAM_REPORT_ERRORS, $openedPath);
        self::assertSame('Only reading is supported', self::$errorString);
    }

    public function testChainedBadPath(): void
    {
        self::assertSame('', self::$errorString);
        $ole = new OLE\ChainedBlockStream();
        $openedPath = '';
        self::assertFalse($ole->stream_open('whatever', 'r', 0, $openedPath));

        $ole->stream_open('whatever', 'r', STREAM_REPORT_ERRORS, $openedPath);
        self::assertSame('OLE stream not found', self::$errorString);
    }

    /**
     * @param mixed[] $bbat
     * @param mixed[] $sbat
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('invalidChainProvider')]
    public function testChainedStreamRejectsInvalidAllocationChains(array $bbat, array $sbat, int $blockId, ?int $size, string $message): void
    {
        $fileHandle = tmpfile();
        self::assertNotFalse($fileHandle);
        $ole = new OLE();
        $ole->_file_handle = $fileHandle;
        $ole->bbat = $bbat;
        $ole->sbat = $sbat;
        $ole->bigBlockSize = 512;
        $ole->smallBlockSize = 64;
        $ole->bigBlockThreshold = 4096;
        $ole->root = new Root(null, null, []);
        $ole->root->startBlock = 0;
        $GLOBALS['_OLE_INSTANCES'] = [$ole];
        $path = 'ole-chainedblockstream://oleInstanceId=0&blockId=' . $blockId . ($size === null ? '' : '&size=' . $size);

        try {
            $this->expectException(Exception::class);
            $this->expectExceptionMessage($message);
            (new OLE\ChainedBlockStream())->stream_open($path, 'r', 0, $openedPath);
        } finally {
            fclose($fileHandle);
            $GLOBALS['_OLE_INSTANCES'] = [];
        }
    }

    /** @return array<string, array{array<mixed>, array<mixed>, int, ?int, string}> */
    public static function invalidChainProvider(): array
    {
        return [
            'cyclic root mini-stream chain' => [[0 => 0], [1 => -2], 1, 1, 'Invalid OLE root mini-stream chain.'],
            'cyclic mini-stream chain' => [[0 => -2], [1 => 1], 1, 1, 'Invalid OLE mini-stream chain.'],
            'cyclic regular stream chain' => [[0 => 0], [], 0, null, 'Invalid OLE stream chain.'],
            'missing regular stream allocation' => [[], [], 0, null, 'Invalid OLE stream chain.'],
        ];
    }
}
